<?php

namespace Notice;

use Interop\Container\ContainerInterface;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class NotifierListener
 */
class NotifierListener
{
    /**
     * @var ContainerInterface
     */
    protected $services;

    /**
     * @var array
     */
    protected $listenerConfig = [];

    /**
     * NotifierListener constructor.
     *
     * @param ContainerInterface $services
     * @param array $listeners
     */
    public function __construct(ContainerInterface $services, array $listeners)
    {
        $this->services       = $services;
        $this->listenerConfig = $listeners;
    }

    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listenerConfig['listeners'] as $serviceKey) {
            $listener = $this->services->get($serviceKey);

            if (!$listener instanceof NoticeInterface) {
                continue;
            }

            $listener->attachShared($manager);
        }
    }
}
