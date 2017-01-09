<?php

namespace Rule\Event\Rule;

use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;

/**
 * Abstract Class for event rules
 */
abstract class AbstractEventRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var string
     */
    protected $eventProviderName;

    /**
     * @var mixed
     */
    protected $expectedValue;

    /**
     * AbstractEventRule constructor.
     *
     * @param string $eventProviderName
     * @param $expectedValue
     */
    public function __construct(string $eventProviderName, $expectedValue)
    {
        $this->eventProviderName = $eventProviderName;
        $this->expectedValue     = $expectedValue;
    }
}
