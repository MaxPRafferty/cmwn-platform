<?php

namespace Security\Authorization;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

/**
 * Class MvcAuthListener
 *
 */
class MvcAuthListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * MvcAuthListener constructor.
     *
     * @param Rbac $rbac
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(Rbac $rbac, AuthenticationServiceInterface $authService)
    {
        $this->rbac        = $rbac;
        $this->authService = $authService;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'setRole']);
    }
    
    public function setRole(MvcEvent $event)
    {
//        $role = $this->authService
        
    }
}
