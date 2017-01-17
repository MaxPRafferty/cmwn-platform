<?php

namespace Application\Listeners;

use Interop\Container\ContainerInterface;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class ListenersAggregate
 * @deprecated Use Rule engineß
 */
class ListenersAggregate
{
    /**
     * @var ContainerInterface
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
     *
     * @param ContainerInterface $services
     * @param array $config
     */
    public function __construct(ContainerInterface $services, $config = [])
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
