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
     * @param ActionCollectionInterface $actionCollection
     */
    public function setActionCollection(ActionCollectionInterface $actionCollection);

    /**
     * Return back an Action Collection
     *
     * @return ActionCollectionInterface
     */
    public function getActionCollection(): ActionCollectionInterface;
}
