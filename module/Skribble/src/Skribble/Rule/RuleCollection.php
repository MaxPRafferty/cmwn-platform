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
    public function __construct($type, $input = null, $flags = 0, $iteratorClass = \ArrayIterator::class)
    {
        parent::__construct($input, $flags, $iteratorClass);
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $return   = [];
        $iterator = $this->getIterator();
        $iterator->rewind();
        while ($iterator->valid()) {
            /** @var RuleCompositeInterface $rule */
            $rule = $iterator->current();
            array_push($return, $rule->getArrayCopy());
        }

        return $return;
    }
}
