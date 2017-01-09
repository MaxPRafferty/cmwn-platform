<?php

namespace Application\Rule\Session\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Zend\Session\Container;

/**
 * A Rule that is satisfied when the session value matches a value from a provider
 */
class ValueEqualsProvider implements RuleInterface
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
     * @var string
     */
    private $providerKey;

    /**
     * ValueEqualsProvider constructor.
     *
     * @param string $containerProvider
     * @param string $containerKey
     * @param string $providerKey
     */
    public function __construct(string $containerProvider, string $containerKey, string $providerKey)
    {
        $this->container    = $containerProvider;
        $this->containerKey = $containerKey;
        $this->providerKey  = $providerKey;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $container = $item->getParam($this->container);
        static::checkValueType($container, Container::class);
        if (!$container->offsetGet($this->containerKey) !== $item->getParam($this->providerKey)) {
            return false;
        }

        $this->timesSatisfied++;

        return true;
    }
}
