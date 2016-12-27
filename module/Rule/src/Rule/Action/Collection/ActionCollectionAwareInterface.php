<?php

namespace Rule\Action\Collection;

/**
 * Interface that can accept a collection of Actions
 */
interface ActionCollectionAwareInterface
{
    /**
     * Sets the action collection for the object
     *
     * This is designed to be fluent
     *
     * @param ActionCollectionInterface $actionCollection
     *
     * @return ActionCollectionAwareInterface
     */
    public function setActionCollection(ActionCollectionInterface $actionCollection): ActionCollectionAwareInterface;

    /**
     * Return back an Action Collection
     *
     * @return ActionCollectionInterface
     */
    public function getActionCollection(): ActionCollectionInterface;
}
