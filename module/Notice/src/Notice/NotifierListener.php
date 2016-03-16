<?php

namespace Notice;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class NotifierListener
 *
 * ${CARET}
 */
class NotifierListener
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @var array
     */
    protected $listenerConfig = [];

    public function __construct(ServiceLocatorInterface $services, array $listeners)
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
