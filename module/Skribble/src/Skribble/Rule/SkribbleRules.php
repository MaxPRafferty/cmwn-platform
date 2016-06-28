<?php

namespace Skribble\Rule;

use Media\MediaInterface;

/**
 * Class SkribbleRules
 */
class SkribbleRules implements RuleCompositeInterface, RuleSpecificationInterface
{
    /**
     * @param RuleCompositeInterface $rule
     */
    public function addRule(RuleCompositeInterface $rule)
    {

    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *
     * @return void
     */
    public function exchangeArray(array $array)
    {
        // TODO: Implement exchangeArray() method.
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        // TODO: Implement getArrayCopy() method.
    }

    /**
     * Tests if this rule is valid or not
     *
     * @return bool
     */
    public function isValid()
    {
        // TODO: Implement isValid() method.
    }

    /**
     * Gets the type of rule
     *
     * @return string
     */
    public function getType()
    {
        // TODO: Implement getType() method.
    }

    /**
     * Sets the media used for this rule
     *
     * @param MediaInterface $media
     *
     * @return RuleCompositeInterface
     */
    public function setMedia(MediaInterface $media)
    {
        // TODO: Implement setMedia() method.
    }

    /**
     * Gets the media for this rule
     *
     * @return MediaInterface
     */
    public function getMedia()
    {
        // TODO: Implement getMedia() method.
    }

}
