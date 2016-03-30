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
        foreach ($this->getListeners() as $listener) {
            if (method_exists($listener, 'attachShared')) {
                $listener->attachShared($events);
            }
        }
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->getListeners() as $listener) {
            if (method_exists($listener, 'detachShared')) {
                $listener->detachShared($events);
            }
        }
    }

    /**
     * @return array
     */
    protected function getListeners()
    {
        if (empty($this->listeners)) {
            foreach ($this->config as $serviceKey) {
                $this->listeners[] = $this->services->get($serviceKey);
            }
        }

        return $this->listeners;
    }
}
