<?php

namespace Application\Utils\Flags;

/**
 * An Interface that defines dealint with bitwise flags on an object
 */
interface FlagInterface
{
    /**
     * Sets the flags on the game
     *
     * @param int $flags
     *
     * @return FlagInterface
     */
    public function setFlags(int $flags): FlagInterface;

    /**
     * Returns the flags that are set on the game
     *
     * @return int
     */
    public function getFlags(): int;

    /**
     * Toggles a flag
     *
     * @param int $flag
     *
     * @return FlagInterface
     */
    public function toggleFlag(int $flag): FlagInterface;

    /**
     * Used to check if a flag state
     *
     * @param int $flag
     *
     * @return bool
     */
    public function hasFlag(int $flag): bool;
}
