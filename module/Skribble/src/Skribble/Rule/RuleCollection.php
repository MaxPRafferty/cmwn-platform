<?php

namespace Skribble\Rule;

/**
 * Class RuleCollection
 */
class RuleCollection extends \ArrayObject implements RuleCompositeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $valid = true;

    /**
     * RuleCollection constructor.
     *
     * @param string $type
     * @param array|null|object $input
     * @param int $flags
     * @param string $iteratorClass
     */
    public function __construct($type, $input = [], $flags = 0, $iteratorClass = \ArrayIterator::class)
    {
        $this->setFlags($flags);
        $this->setIteratorClass($iteratorClass);
        $this->exchangeArray($input);
        $this->type = $type;
    }

    /**
     * Ensures that we only have rule objects in this collection
     *
     * @param mixed $index
     * @param mixed $rule
     */
    public function offsetSet($index, $rule)
    {
        if (!$rule instanceof RuleInterface) {
            throw new \InvalidArgumentException('Only instances of RuleInterfaces can be set');
        }

        $this->valid = $this->valid && $rule->isValid();
        parent::offsetSet($index, $rule);
    }

    /**
     * Tests if this rule is valid or not
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Gets the type of rule
     *
     * @return string
     */
    public function getRuleType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $return = [];

        // Transform all rules into arrays
        foreach (parent::getArrayCopy() as $key => $value) {
            $return[$key] = $value instanceof RuleInterface ? $value->getArrayCopy() : $value;
        }

        return $return;
    }

    /**
     * @param array $input
     * @return array
     */
    public function exchangeArray($input)
    {
        foreach ($input as $ruleOptions) {
            $this->append(RuleStaticFactory::createRuleFromArray($ruleOptions));
        }
    }
}
