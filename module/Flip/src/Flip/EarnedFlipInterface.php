<?php

namespace Flip;

/**
 * An Expanded flip that defines when the flip was earned by a user
 */
interface EarnedFlipInterface extends FlipInterface
{
    /**
     * Gets the date the flip was earned for a user
     *
     * @return \DateTime|null
     */
    public function getEarned();

    /**
     * Sets the date the flip was earned
     *
     * @param \DateTime $earned
     */
    public function setEarned(\DateTime $earned = null);

    /**
     * Sets the Id used to Acknowledge the flip was earned
     *
     * @param string $ackId
     */
    public function setAcknowledgeId(string $ackId);

    /**
     * Gets the Id used to acknowledge the flip
     *
     * @return string
     */
    public function getAcknowledgeId(): string;

    /**
     * Weather the earned flip has been acknowledged
     *
     * @return bool
     */
    public function isAcknowledged(): bool;
}
