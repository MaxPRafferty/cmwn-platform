<?php

namespace Rule\Engine\Specification;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionCollectionInterface;
use Rule\Item\RuleItemInterface;
use Rule\RuleCollectionInterface;

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
     * @param ContainerInterface $services
     *
     * @return RuleCollectionInterface
     */
    public function getRules(ContainerInterface $services): RuleCollectionInterface;

    /**
     * Allows the specification to build all the actions from the container
     *
     * @param ContainerInterface $services
     *
     * @return ActionCollectionInterface
     */
    public function getActions(ContainerInterface $services): ActionCollectionInterface;

    /**
     * Builds the rule from the specification so it can set the correct providers
     *
     * @param ContainerInterface $services
     *
     * @return RuleItemInterface
     */
    public function buildItem(ContainerInterface $services): RuleItemInterface;
}
