<?php

namespace Rule\Provider;

use Zend\EventManager\EventInterface;

/**
 * A Provider that will just pass through the value passed into it
 */
class BasicValueProvider implements ProviderInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $name;

    /**
     * @inheritDoc
     */
    public function __construct(string $name, $value)
    {
        $this->value = $value;
        $this->name  = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName(EventInterface $event = null): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getValue(EventInterface $event = null)
    {
        return $this->value;
    }

}
