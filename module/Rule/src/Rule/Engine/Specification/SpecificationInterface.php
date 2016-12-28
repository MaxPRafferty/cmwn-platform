<?php

namespace Rule\Engine\Specification;

use Interop\Container\ContainerInterface;
use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\Service\RuleManager;

/**
 * A Specification the engine uses to build and run rules
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
     * @param ContainerInterface $services
     *
     * @return \Rule\Action\Collection\ActionCollectionInterface
     */
    public function getActions(ContainerInterface $services): ActionCollectionInterface;

    /**
     * Builds the rule from the specification so it can set the correct providers
     *
     * @param ContainerInterface $services
     *
     * @return \Rule\Provider\Collection\ProviderCollectionInterface
     */
    public function buildProvider(ContainerInterface $services): ProviderCollectionInterface;
}
