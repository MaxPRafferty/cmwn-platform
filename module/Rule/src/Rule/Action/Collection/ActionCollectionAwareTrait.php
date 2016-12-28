<?php

namespace Rule\Action\Collection;

/**
 * Trait ActionCollectionAwareTrait
 */
trait ActionCollectionAwareTrait
{
    /**
     * @var ActionCollectionInterface
     */
    protected $actionCollection;

    /**
     * Sets the action collection for the object
     *
     * This is designed to be fluent
     *
     * @param ActionCollectionInterface $actionCollection
     *
     * @return ActionCollectionAwareInterface
     */
    public function setActionCollection(ActionCollectionInterface $actionCollection)
    {
        $this->actionCollection = $actionCollection;
    }

    /**
     * Return back an Action Collection
     *
     * @return ActionCollectionInterface
     */
    public function getActionCollection(): ActionCollectionInterface
    {
        return $this->actionCollection;
    }
}
