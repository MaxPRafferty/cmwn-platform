<?php

namespace Api\Listeners;

use Api\Links\OrgUserLink;
use Org\OrganizationInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\SecurityUser;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Stdlib\CallbackHandler;
use ZF\Hal\Entity;
use ZF\Hal\Plugin\Hal;

/**
 * Class OrgLinkListener
 */
class OrgLinkListener implements AuthenticationServiceAwareInterface, RbacAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use RbacAwareTrait;

    /**
     * @var CallbackHandler
     */
    protected $listener;

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listener = $events->attach(Hal::class, 'renderEntity', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $events->detach(Hal::class, $this->listener);
    }

    /**
     * @param Event $event
     */
    public function onRender(Event $event)
    {
        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;
        if (!$realEntity instanceof OrganizationInterface) {
            return;
        }

        // Should never be able to load a scope object
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        /** @var SecurityUser $authUser */
        $authUser = $this->getAuthenticationService()->getIdentity();
        if (!$this->getRbac()->isGranted($authUser->getRole(), 'view.org.users')) {
            return;
        }

        $entity->getLinks()->add(new OrgUserLink($realEntity));
    }
}
