<?php

namespace Application\Rule\Session\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Zend\Session\Container;

/**
 * A Rule that is satisfied when the session value matches a value
 */
class ValueEquals implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $containerKey;

    /**
     * @var mixed
     */
    private $expectedValue;

    /**
     * ValueEquals constructor.
     *
     * @param string $containerProvider
     * @param string $containerKey
     * @param $expectedValue
     */
    public function __construct(string $containerProvider, string $containerKey, $expectedValue)
    {
        $this->container     = $containerProvider;
        $this->containerKey  = $containerKey;
        $this->expectedValue = $expectedValue;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $container = $item->getParam($this->container);
        static::checkValueType($container, Container::class);
        if (!$container->offsetGet($this->containerKey) !== $this->expectedValue) {
            return false;
        }

        $this->timesSatisfied++;

        return true;
    }
}
