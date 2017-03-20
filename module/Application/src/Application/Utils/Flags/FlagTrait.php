<?php

namespace Application\Utils\Flags;

/**
 * A Trait that helps satisfy FlagInterface
 */
trait FlagTrait
{
    /**
     * @var int
     */
    protected $flags = 0;

    /**
     * Sets the flags on the game
     *
     * @param int $flags
     *
     * @return FlagInterface
     */
    public function setFlags(int $flags): FlagInterface
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * Returns the flags that are set on the game
     *
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * Toggles a flag
     *
     * @param int $flag
     *
     * @return FlagInterface
     */
    public function toggleFlag(int $flag): FlagInterface
    {
        if ($this->hasFlag($flag)) {
            $flag *= -1;
        }

        $this->flags += $flag;

        return $this;
    }

    /**
     * Used to check if a flag state
     *
     * @param int $flag
     *
     * @return bool
     */
    public function hasFlag(int $flag): bool
    {
        return ($this->flags & $flag) == $flag;
    }
}
