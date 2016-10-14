<?php

namespace Application\Listeners;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ListenersAggregate
 */
class ListenersAggregate
{
    /**
     * @var ServiceLocatorInterface
     */
    public $services;

    /**
     * @var array
     */
    public $config = [];

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * ListenersAggregate constructor.
     * @param ServiceLocatorInterface $services
     * @param array $config
     */
    public function __construct(ServiceLocatorInterface $services, $config = [])
    {
        $this->services = $services;
        $this->config   = $config;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->config as $serviceKey) {
            $listener = $this->services->get($serviceKey);
            if (method_exists($listener, 'attachShared')) {
                $listener->attachShared($events);
            }

            array_push($this->listeners, $listener);
        }
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            if (method_exists($listener, 'detachShared')) {
                $listener->detachShared($events);
            }
        }
    }
}
