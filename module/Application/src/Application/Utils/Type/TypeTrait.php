<?php

namespace Application\Utils\Type;

/**
 * A Trait that helps satisfy the TypeInterface
 *
 * @see TypeInterface
 *
 */
trait TypeTrait
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Sets the type property
     *
     * @param string $type
     *
     * @return TypeInterface
     */
    public function setType(string $type)
    {
        $type = empty($type) ? TypeInterface::TYPE_GENERIC : $type;

        if (!defined(TypeInterface::class . '::TYPE_' . strtoupper($type))) {
            throw new \RuntimeException('Invalid Type: ' . $type);
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Gets the type
     *
     * SHOULD return static::TYPE_GENERIC
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
