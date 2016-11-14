<?php

namespace RestoreDb\Listener;

use Application\Exception\PreConditionFailedException;
use RestoreDb\Service\RestoreDbServiceInterface;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class CheckConfigListener
 * @package RestoreDb\Listener
 */
class CheckConfigListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var bool
     */
    protected $allowed;

    /**
     * CheckConfigListener constructor.
     * @param bool $allowed
     */
    public function __construct($allowed = false)
    {
        $this->allowed = $allowed;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            RestoreDbServiceInterface::class,
            'restore.db.state',
            [$this, 'restoreDbState']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(RestoreDbServiceInterface::class, $this->listeners[0]);
    }

    public function restoreDbState()
    {
        if (! (bool) $this->allowed) {
            throw new PreConditionFailedException();
        }
    }
}
