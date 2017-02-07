<?php

namespace RuleTest\Action\Service;

use Rule\Action\ActionInterface;
use Rule\Action\NoopAction;

/**
 * Class ActionWithDependency
 */
class ActionWithDependency extends NoopAction implements ActionInterface
{
    /**
     * @var ActionDependency
     */
    public $depend;

    /**
     * @inheritDoc
     */
    public function __construct(ActionDependency $depend)
    {
        $this->depend = $depend;
    }
}
