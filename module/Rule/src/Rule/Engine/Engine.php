<?php

namespace Rule\Engine;

use Interop\Container\ContainerInterface;
use Rule\Engine\Specification\SpecificationCollectionInterface;
use Rule\Engine\Specification\SpecificationInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Stdlib\CallbackHandler;

/**
 * The rules engine in all it's glory
 */
class Engine
{
    /**
     * Engine constructor.
     *
     * @param SharedEventManagerInterface $events
     * @param ContainerInterface $container
     * @param SpecificationCollectionInterface|SpecificationInterface[] $specs
     */
    public function __construct(
        SharedEventManagerInterface $events,
        ContainerInterface $container,
        SpecificationCollectionInterface $specs
    ) {
        foreach ($specs as $spec) {
            $events->attach('*', $spec->getEventName(), new EngineHandler($container, $spec));
        }
    }
}
