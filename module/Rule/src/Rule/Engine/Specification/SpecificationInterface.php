<?php

namespace Rule\Engine\Specification;

use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Action\Service\ActionManager;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\Service\ProviderManager;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\Service\RuleManager;

/**
 * A Specification the engine uses to check rules and run actions on an event
 */
interface SpecificationInterface
{
    /**
     * The identifier of this specification
     *
     * Useful for storing the the database
     *
     * @return string
     */
    public function getId(): string;

    /**
     * A Human readable name of this specification
     *
     * @return string
     */
    public function getName(): string;

    /**
     * The Name of the event to listen for
     *
     * @return string
     */
    public function getEventName(): string;

    /**
     * Allows the specification to build all the rules from the container
     *
     * @param RuleManager $ruleManager
     *
     * @return RuleCollectionInterface
     */
    public function getRules(RuleManager $ruleManager): RuleCollectionInterface;

    /**
     * Allows the specification to build all the actions from the container
     *
     * @param ActionManager $actionManager
     *
     * @return \Rule\Action\Collection\ActionCollectionInterface
     */
    public function getActions(ActionManager $actionManager): ActionCollectionInterface;

    /**
     * Builds the rule from the specification so it can set the correct providers
     *
     * @param ProviderManager $providerManager
     *
     * @return \Rule\Provider\Collection\ProviderCollectionInterface
     */
    public function buildProvider(ProviderManager $providerManager): ProviderCollectionInterface;
}
