<?php

namespace Rule\Engine;

use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Stdlib\CallbackHandler;

/**
 * Class Engine
 */
class Engine
{
    /**
     * @var EngineSpecificationInterface[]
     */
    protected $specs;

    /**
     * @var CallbackHandler
     */
    protected $listener;

    /**
     * Engine constructor.
     *
     * @param EngineSpecificationInterface[] ...$specs
     */
    public function __construct(EngineSpecificationInterface...$specs)
    {
        $this->specs = $specs;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listener = $events->attach('*', '*', [$this, 'handleEvent']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $events->detach('*', $this->listener);
    }

    public function handleEvent(EventInterface $event)
    {

    }
}
