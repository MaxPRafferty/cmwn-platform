<?php

namespace Api\Listeners;

use Api\Links\ResetLink;
use Api\V1\Rest\User\MeEntity;
use Friend\Service\FriendServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Service\SecurityGroupServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class ResetHalLinkListener
 */
class ResetHalLinkListener implements AuthenticationServiceAwareInterface, RbacAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use RbacAwareTrait;

    /**
     * @var FriendServiceInterface
     */
    protected $securityGroupService;

    /**
     * @var array
     */
    protected $listeners = [];

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
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $events->detach('ZF\Hal\Plugin\Hal', $this->listeners[1]);
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

        /** @var UserInterface $authUser */
        $authUser = $this->getAuthenticationService()->getIdentity();
        $entity   = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;
        if (!$realEntity instanceof UserInterface) {
            return;
        }

        $permission = $realEntity->getType() . '.code';
        $role       = $this->securityGroupService->fetchRelationshipRole($authUser, $realEntity);

        if ($this->getRbac()->isGranted($role, $permission)) {
            $entity->getLinks()->add(new ResetLink($realEntity->getUserId()));
        }
    }
}
