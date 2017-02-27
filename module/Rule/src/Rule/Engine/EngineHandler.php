<?php

namespace Rule\Engine;

use Rule\Action\Service\ActionManager;
use Rule\Engine\Specification\SpecificationInterface;
use Rule\Exception\RuntimeException;
use Rule\Item\EventRuleItem;
use Rule\Provider\Service\ProviderManager;
use Rule\Rule\Service\RuleManager;
use Zend\EventManager\EventInterface;

/**
 * Class EngineListener
 */
class EngineHandler
{
    /**
     * @var SpecificationInterface
     */
    protected $spec;

    /**
     * @var RuleManager
     */
    protected $ruleManager;

    /**
     * @var ActionManager
     */
    protected $actionManager;

    /**
     * @var ProviderManager
     */
    protected $providerManager;

    /**
     * EngineHandler constructor.
     *
     * @param ActionManager $actionManager
     * @param RuleManager $ruleManager
     * @param ProviderManager $providerManager
     */
    public function __construct(
        ActionManager $actionManager,
        RuleManager $ruleManager,
        ProviderManager $providerManager
    ) {
        $this->ruleManager     = $ruleManager;
        $this->providerManager = $providerManager;
        $this->actionManager   = $actionManager;
    }

    /**
     * Removes the current spec when cloning
     */
    public function __clone()
    {
        $this->spec = null;
    }

    /**
     * @param SpecificationInterface $specification
     */
    public function setSpecification(SpecificationInterface $specification)
    {
        $this->spec = $specification;
    }

    /**
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event)
    {
        if (null === $this->spec) {
            throw new RuntimeException('Cannot handle "%s", specification is not set');
        }

        $item = new EventRuleItem($event);
        $item->setSpecification($this->spec);
        $item->setProviderCollection($this->spec->buildProvider($this->providerManager));
        if (!$this->spec->getRules($this->ruleManager)->isSatisfiedBy($item)) {
            return;
        }

        $actions = $this->spec->getActions($this->actionManager);
        $actions($item);
    }
}
