<?php

namespace Rule\Provider;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }
}
