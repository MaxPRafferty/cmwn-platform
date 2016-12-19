<?php

namespace Rule\Provider;

/**
 * Class ProviderCollection
 */
class ProviderCollection implements ProviderCollectionInterface
{
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
        $this->parameters = new \ArrayObject();
        array_walk($providers, [$this, 'append']);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->parameters->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function append(ProviderInterface $provider): ProviderCollectionInterface
    {
        if (!$this->offsetExists($provider->getName())) {
            $this->offsetSet($provider->getName(), $provider->getValue());
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
        return $this->parameters->offsetGet($offset);
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
}
