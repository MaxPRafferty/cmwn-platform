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
     * Designed to be fluent
     *
     * @param \DateTime|null $earned
     *
     * @return EarnedFlipInterface
     */
    public function setEarned(\DateTime $earned = null): EarnedFlipInterface;

    /**
     * Sets the Id used to Acknowledge the flip was earned
     *
     * Designed to be fluent
     *
     * @param string $ackId
     *
     * @return EarnedFlipInterface
     */
    public function setAcknowledgeId(string $ackId): EarnedFlipInterface;

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

    /**
     * Sets the Id of the user that earned the flip
     *
     * Designed to be fluent
     *
     * @param string $userId
     *
     * @return EarnedFlipInterface
     */
    public function setEarnedBy(string $userId): EarnedFlipInterface;

    /**
     * Returns the Id of the user that earned the flip
     *
     * @return string
     */
    public function getEarnedBy(): string;
}
