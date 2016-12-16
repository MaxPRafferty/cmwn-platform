<?php

namespace Rule\Action;

use Rule\Item\RuleItemInterface;

/**
 * Class ActionCollection
 */
class ActionCollection implements ActionCollectionInterface
{
    /**
     * @var \ArrayIterator|ActionInterface[]
     */
    protected $actions;

    /**
     * Sets up the array iterator for actions
     */
    public function __construct()
    {
        $this->actions = new \ArrayIterator([]);
    }

    /**
     * @inheritDoc
     */
    public function append(ActionInterface $action): ActionCollectionInterface
    {
        $this->actions->append($action);
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->actions;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        foreach ($this->actions as $action) {
            $action->__invoke($item);
        }
    }
}
