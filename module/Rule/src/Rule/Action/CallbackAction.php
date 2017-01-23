<?php

namespace Rule\Action;

use Rule\Item\RuleItemInterface;

/**
 * Class CallbackAction
 */
class CallbackAction implements ActionInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * CallbackAction constructor.
     *
     * @param callable $callBack
     */
    public function __construct(callable $callBack)
    {
        $this->callback = $callBack;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        call_user_func($this->callback, $item);
    }
}
