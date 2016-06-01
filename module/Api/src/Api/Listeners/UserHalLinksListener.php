<?php

namespace Api\Listeners;

use Api\Links\ForgotLink;
use Api\Links\PasswordLink;
use Api\Links\ResetLink;
use Api\V1\Rest\User\MeEntity;
use Api\V1\Rest\User\UserEntity;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\SecurityUser;
use Security\Service\SecurityGroupServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Plugin\Hal;

/**
 * Class UserHalLinksListener
 *
 * Adjusts the hal links for a user based on the user permissions
 */
class UserHalLinksListener implements AuthenticationServiceAwareInterface, RbacAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use RbacAwareTrait;

    /**
     * @var SecurityGroupServiceInterface
     */
    protected $securityGroupService;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var SecurityUser
     */
    protected $authUser;

    /**
     * @var UserEntity
     */
    protected $entityUser;

    /**
     * ResetHalLinkListener constructor.
     *
     * @param SecurityGroupServiceInterface $securityGroupService
     */
    public function __construct(SecurityGroupServiceInterface $securityGroupService)
    {
        $this->securityGroupService = $securityGroupService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(Hal::class, 'renderEntity', [$this, 'onRender']);
        $this->listeners[] = $events->attach(Hal::class, 'renderCollection.entity', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach(Hal::class, $listener);
        }
    }

    /**
     * @param Event $event
     */
    public function onRender(Event $event)
    {
        // Should never be able to load a scope object
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        $entity     = $event->getParam('entity');

        // renderCollection will set the entity param as the actual entity
        // renderEntity will set the entity param as the HalEntity
        $realEntity = $entity instanceof Entity ? $entity->entity : $entity;
        if (!$realEntity instanceof UserEntity) {
            return;
        }

        $this->authUser   = $this->getAuthenticationService()->getIdentity();
        $this->entityUser = $realEntity;
        $this->checkHalLinks();
    }

    protected function checkHalLinks()
    {
        $links = $this->entityUser->getLinks();
        $this->checkForgotLink($links)
            ->checkPasswordLink($links);
    }

    /**
     * Adds the forgot password if the logged in user is allowed to change that users password
     *
     * @param LinkCollection $links
     * @return $this
     */
    protected function checkPasswordLink(LinkCollection $links)
    {
        if ($links->has('reset')) {
            return $this;
        }

        $permission = strtolower($this->entityUser->getType()) . '.code';
        if ($this->entityUser->getType() === UserEntity::TYPE_CHILD && $this->checkPermissionHelper($permission)) {
            $links->add(new ResetLink($this->entityUser->getUserId()));
        }

        return $this;
    }

    /**
     * Checks if the forgot link is allowed
     *
     * @param LinkCollection $links
     * @return $this
     */
    protected function checkForgotLink(LinkCollection $links)
    {
        if ($this->entityUser instanceof MeEntity || $links->has('forgot')) {
            return $this;
        }

        $permission = strtolower($this->entityUser->getType()) . '.code';
        if ($this->entityUser->getType() === UserEntity::TYPE_ADULT && $this->checkPermissionHelper($permission)) {
            $links->add(new ForgotLink());
        }

        return $this;
    }

    /**
     * Helps check the permission for a hal link
     *
     * @param $permission
     * @return bool
     */
    protected function checkPermissionHelper($permission)
    {
        $role = $this->authUser->getRole() !== 'super'
            ? $this->securityGroupService->fetchRelationshipRole($this->authUser, $this->entityUser)
            : $this->authUser->getRole();

        return $this->getRbac()->isGranted($role, $permission);
    }
}
