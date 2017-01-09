<?php

namespace Rule\Engine;

use Rule\Action\Service\ActionManager;
use Rule\Engine\Specification\SpecificationCollectionInterface;
use Rule\Provider\Service\ProviderManager;
use Rule\Rule\Service\RuleManager;
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
     * @param ActionManager $actionManager
     * @param RuleManager $ruleManager
     * @param ProviderManager $providerManager
     * @param SpecificationCollectionInterface $specs
     */
    public function __construct(
        SharedEventManagerInterface $events,
        ActionManager $actionManager,
        RuleManager $ruleManager,
        ProviderManager $providerManager,
        SpecificationCollectionInterface $specs
    ) {
        $handler = new EngineHandler(
            $actionManager,
            $ruleManager,
            $providerManager
        );

        foreach ($specs as $spec) {
            $newHandler = clone $handler;
            $newHandler->setSpecification($spec);
            $events->attach(
                '*',
                $spec->getEventName(),
                $newHandler
            );
        }
    }
}
