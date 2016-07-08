<?php

namespace Skribble\Rule;

/**
 * Interface RuleCompositeInterface
 */
interface RuleCompositeInterface
{
    const TYPE_EFFECT     = 'effect';
    const TYPE_SOUND      = 'sound';
    const TYPE_BACKGROUND = 'background';
    const TYPE_ITEM       = 'item';
    const TYPE_MESSAGE    = 'message';

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *
     * @return void
     */
    public function exchangeArray(array $array);

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * Tests if this rule is valid or not
     *
     * @return bool
     */
    public function isValid();

    /**
     * Gets the type of rule
     *
     * @return string
     */
    public function getType();
}
