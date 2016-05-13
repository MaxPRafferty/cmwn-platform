<?php

namespace Flip;

/**
 * Interface EarnedFlipInterface
 */
interface EarnedFlipInterface extends FlipInterface
{
    /**
     * Gets the date the flip was earned for a user
     * @return \DateTime
     */
    public function getEarned();

    /**
     * @param \DateTime $earned
     */
    public function setEarned(\DateTime $earned = null);
}
