<?php

namespace Rule\Provider\Collection;

use Rule\Item\RuleItemInterface;
use Rule\Item\RuleItemTrait;
use Rule\Provider\ProviderInterface;

/**
 * A collection of providers
 */
class ProviderCollection implements ProviderCollectionInterface
{
    use RuleItemTrait;

    /**
     * @var \ArrayObject
     */
    protected $parameters;

    /**
     * ProviderCollection constructor.
     *
     * @param ProviderInterface[] ...$providers
     */
    public function __construct(ProviderInterface ...$providers)
    {
        $this->parameters = new \ArrayObject([], null, ProviderIterator::class);
        array_walk($providers, [$this, 'append']);
    }

    /**
     * @inheritDoc
     * @return \ArrayIterator|ProviderInterface[]
     */
    public function getIterator()
    {
        return $this->parameters->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function append(ProviderInterface $provider): RuleItemInterface
    {
        if (!$this->offsetExists($provider->getName())) {
            $this->offsetSet($provider->getName(), $provider);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->parameters->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if (!$this->parameters->offsetExists($offset)) {
            return null;
        }

        $value = $this->parameters->offsetGet($offset);
        return $value instanceof ProviderInterface ? $value->getValue() : $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->parameters->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->parameters->offsetUnset($offset);
    }

    /**
     * @inheritDoc
     */
    public function getParam(string $param, $default = null)
    {
        return $this->offsetExists($param) ? $this->offsetGet($param) : $default;
    }

    /**
     * @inheritDoc
     */
    public function getProvider($name): ProviderInterface
    {
        if (!$this->parameters->offsetExists($name)) {
            return null;
        }

        return $this->parameters->offsetGet($name);
    }
}
