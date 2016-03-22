<?php

namespace Api\Listeners;

use Api\Links\ImportLink;
use Group\GroupInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\SecurityUser;
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
     */
    public function onRender(Event $event)
    {
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

        if ($this->getRbac()->isGranted($this->getRole(), 'import')) {
            $realEntity->getLinks()->add(new ImportLink($realEntity));
        }
    }

    /**
     * @return string
     */
    protected function getRole()
    {
        if ($this->role !== null) {
            return $this->role;
        }

        $user = $this->authService->getIdentity();
        $role = 'guest';
        if ($user instanceof SecurityUser) {
            $role = $user->getRole();
        }

        $this->role = $role;
        return $this->role;
    }
}
