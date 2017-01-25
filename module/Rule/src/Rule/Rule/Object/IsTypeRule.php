<?php

namespace Rule\Rule\Object;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;

/**
 * This rule determines if the object passed in is a certain object type.
 */
class IsTypeRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var string
     */
    protected $objectType;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * IsTypeRule constructor.
     * @param string $objectType
     * @param string $providerName
     */
    public function __construct(string $objectType, string $providerName)
    {

        $this->objectType = $objectType;
        $this->providerName = $providerName;
    }

    /**
     * @inheritdoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $object = $item->getParam($this->providerName);
        if ($object instanceof $this->objectType) {
            $this->timesSatisfied++;
            return true;
        }
        return false;
    }
}
