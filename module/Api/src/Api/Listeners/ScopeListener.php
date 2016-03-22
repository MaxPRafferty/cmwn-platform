<?php

namespace Api\Listeners;

use Api\ScopeAwareInterface;
use Security\Authorization\Rbac;
use Security\SecurityUser;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class ScopeListener
 */
class ScopeListener
{
    protected $listeners = [];

    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var string
     */
    protected $role;

    public function __construct(Rbac $rbac, AuthenticationServiceInterface $authService)
    {
        $this->rbac        = $rbac;
        $this->authService = $authService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'onRender']);
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach('ZF\Hal\Plugin\Hal', $listener);
        }
    }

    /**
     * @param EventInterface $event
     */
    public function onRender(EventInterface $event)
    {
        $entity  = $event->getParam('entity');
        $payload = $event->getParam('payload');
        $role    = $this->getRole();
        if (!$entity instanceof Entity) {
            return;
        }

        if ($entity->entity instanceof ScopeAwareInterface) {
            $payload['scope'] =$this->rbac->getScopeForEntity($role, $entity->entity->getEntityType());
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
