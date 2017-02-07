<?php

namespace Application\Rule\Session\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Zend\Session\Container;

/**
 * A Rule that is satisfied when the session contains a key
 */
class HasValue implements RuleInterface
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
     * SessionValue constructor.
     *
     * @param string $containerProvider
     * @param string $containerKey
     */
    public function __construct(string $containerProvider, string $containerKey)
    {
        $this->container    = $containerProvider;
        $this->containerKey = $containerKey;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $container = $item->getParam($this->container);
        static::checkValueType($container, Container::class);
        if (!$container->offsetExists($this->containerKey)) {
            return false;
        }

        $this->timesSatisfied++;

        return true;
    }
}
