<?php

namespace Skribble\Rule;

use Media\MediaInterface;

/**
 * Interface RuleInterface
 */
interface RuleInterface extends RuleCompositeInterface
{
    /**
     * Sets the media used for this rule
     *
     * @param MediaInterface $media
     *
     * @return RuleCompositeInterface
     */
    public function setMedia(MediaInterface $media);

    /**
     * Gets the media for this rule
     *
     * @return MediaInterface
     */
    public function getMedia();
}
