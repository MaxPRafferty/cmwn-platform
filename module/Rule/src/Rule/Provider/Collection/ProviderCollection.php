<?php

namespace Rule\Provider\Collection;

use Rule\Event\Provider\EventProviderInterface;
use Rule\Item\RuleItemInterface;
use Rule\Provider\ProviderInterface;
use Zend\EventManager\EventInterface;

/**
 * A collection of providers
 */
class ProviderCollection implements ProviderCollectionInterface
{
    /**
     * @var \ArrayObject
     */
    protected $parameters;

    /**
     * @var array All the keys that are event providers
     */
    protected $eventProviders = [];

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
     * @return \ArrayIterator|ProviderInterface[]
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

        if ($provider instanceof EventProviderInterface && !in_array($provider->getName(), $this->eventProviders)) {
            array_push($this->eventProviders, $provider->getName());
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

    /**
     * @inheritDoc
     */
    public function setEvent(EventInterface $event): RuleItemInterface
    {
        array_walk($this->eventProviders, function ($providerName) use (&$event) {
            $this->offsetGet($providerName)->setEvent($event);
        });

        return $this;
    }
}
