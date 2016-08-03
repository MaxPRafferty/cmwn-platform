<?php

namespace Skribble\Rule;

/**
 * Class Message
 */
class Message extends AbstractRule implements StateRuleInterface
{
    use StateRuleTrait;

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return $this->isStateValid();
    }

    /**
     * @inheritDoc
     */
    public function getRuleType()
    {
        return 'message';
    }
}
