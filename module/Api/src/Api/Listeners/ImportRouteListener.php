<?php

namespace Api\Listeners;

use Api\Links\ImportLink;
use Group\GroupInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\Service\SecurityOrgServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class ImportRouteListener
 */
class ImportRouteListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var string
     */
    protected $role;

    /**
     * @var SecurityOrgServiceInterface
     */
    protected $orgService;

    /**
     * ImportRouteListener constructor.
     * @param SecurityOrgServiceInterface $orgService
     */
    public function __construct(SecurityOrgServiceInterface $orgService)
    {
        $this->orgService = $orgService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach('ZF\Hal\Plugin\Hal', $listener);
        }
    }

    /**
     * @param Event $event
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function onRender(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof GroupInterface) {
            return;
        }

        if (!$realEntity instanceof LinkCollectionAwareInterface) {
            return;
        }

        try {
            $user = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        $role = $user->isSuper() ? $user->getRole() : $this->orgService->getRoleForGroup($realEntity, $user);
        if ($this->getRbac()->isGranted($role, 'import')) {
            $realEntity->getLinks()->add(new ImportLink($realEntity));
        }
    }
}
