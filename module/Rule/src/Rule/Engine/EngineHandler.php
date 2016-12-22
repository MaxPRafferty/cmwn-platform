<?php

namespace Rule\Engine;

use Interop\Container\ContainerInterface;
use Rule\Engine\Specification\SpecificationInterface;
use Rule\Event\EventRuleItem;
use Rule\Item\BasicRuleItem;
use Zend\EventManager\EventInterface;

/**
 * Class EngineListener
 */
class EngineHandler
{
    /**
     * @var EngineSpecificationInterface
     */
    protected $spec;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * EngineHandler constructor.
     *
     * @param ContainerInterface $container
     * @param SpecificationInterface $specification
     */
    public function __construct(ContainerInterface $container, SpecificationInterface $specification)
    {
        $this->spec      = $specification;
        $this->container = $container;
    }

    /**
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event)
    {
        $provider = $this->spec->buildProvider($this->container);
        $provider->setEvent($event);
        if (!$this->spec->getRules($this->container)->isSatisfiedBy($provider)) {
            return;
        }

        $actions = $this->spec->getActions($this->container);
        $actions($provider);
    }
}
