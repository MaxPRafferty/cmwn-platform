<?php

namespace Flip;

use Application\Utils\Date\DateTimeFactory;

/**
 * This is an earned flip for a user
 */
class EarnedFlip extends Flip implements EarnedFlipInterface
{
    /**
     * @var \DateTime
     */
    protected $earned;

    /**
     * @var string
     */
    protected $ackId;

    /**
     * @var string
     */
    protected $earnedBy;

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array)
    {
        $earned                  = isset($array['earned']) ? DateTimeFactory::factory($array['earned']) : null;
        $array['earned']         = $earned;
        $array['acknowledge_id'] = $array['acknowledge_id'] ?? '';
        parent::exchangeArray($array);
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        $return                   = parent::getArrayCopy();
        $return['earned']         = $this->getEarned() !== null ? $this->getEarned()->format(\DateTime::ISO8601) : null;
        $return['acknowledge_id'] = $this->getAcknowledgeId();
        $return['earned_by']      = $this->getEarnedBy();

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function getEarned()
    {
        return $this->earned;
    }

    /**
     * @inheritdoc
     */
    public function setEarned(\DateTime $earned = null): EarnedFlipInterface
    {
        $this->earned = $earned;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAcknowledgeId(string $ackId): EarnedFlipInterface
    {
        $this->ackId = $ackId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAcknowledgeId(): string
    {
        return (string)$this->ackId;
    }

    /**
     * @inheritDoc
     */
    public function isAcknowledged(): bool
    {
        return empty($this->ackId);
    }

    /**
     * @inheritDoc
     */
    public function setEarnedBy(string $userId): EarnedFlipInterface
    {
        $this->earnedBy = $userId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEarnedBy(): string
    {
        return (string)$this->earnedBy;
    }
}
