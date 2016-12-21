<?php

namespace Rule\Rule\Collection;

use Rule\Rule\Basic\EitherRule;
use Rule\Rule\Basic\NotRule;
use Rule\Exception\InvalidArgumentException;
use Rule\Item\RuleItemInterface;
use Rule\Rule\Collection;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;

/**
 * A Collection of rules
 *
 * This can also be Used as the AndRule
 */
class RuleCollection implements RuleCollectionInterface, \Countable
{
    use TimesSatisfiedTrait;

    /**
     * @var \ArrayObject|RuleInterface[]
     */
    protected $rules;

    /**
     * Sets up the iterator for the rules
     */
    public function __construct()
    {
        $this->rules = new \ArrayObject();
    }

    /**
     * @inheritDoc
     * @return RuleInterface[]|\ArrayIterator
     */
    public function getIterator()
    {
        return $this->rules->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return $this->rules->count();
    }

    /**
     * @inheritDoc
     */
    public function append(
        RuleInterface $rule,
        string $operator = self::OPERATOR_AND,
        string $orGroup = null
    ): Collection\RuleCollectionInterface {
        switch ($operator) {
            case static::OPERATOR_NOT:
                $this->rules->append(new NotRule($rule));
                break;

            case static::OPERATOR_AND:
                $this->rules->append($rule);
                break;

            case static::OPERATOR_OR:
                if (empty($orGroup)) {
                    throw new InvalidArgumentException(
                        'Cannot set Or rule with out a group set'
                    );
                }

                if (!$this->rules->offsetExists($orGroup)) {
                    $this->rules->offsetSet($orGroup, new EitherRule());
                }

                $this->rules->offsetGet($orGroup)->append($rule);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        if (count($this) < 1) {
            return false;
        }

        foreach ($this as $rule) {
            if ($rule->isSatisfiedBy($event)) {
                $this->timesSatisfied++;
            }
        };

        return $this->timesSatisfied === $this->rules->count();
    }
}
