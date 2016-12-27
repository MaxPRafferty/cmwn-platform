<?php

namespace Rule\Action\Collection;

use Rule\Action\ActionInterface;

/**
 * An interface that defines a collection of ActionInterfaces
 */
interface ActionCollectionInterface extends \IteratorAggregate, ActionInterface
{
    /**
     * Adds an action to the collection
     *
     * @param ActionInterface $action
     *
     * @return ActionCollectionInterface
     */
    public function append(ActionInterface $action): ActionCollectionInterface;
}
