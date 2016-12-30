<?php

namespace Flip;

use Application\Utils\Date\DateTimeFactory;

/**
 * This is an earned flip
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
     * @inheritdoc
     */
    public function exchangeArray(array $array)
    {
        $earned          = isset($array['earned']) ? DateTimeFactory::factory($array['earned']) : null;
        $array['earned'] = $earned;
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
    public function setEarned(\DateTime $earned = null)
    {
        $this->earned = $earned;
    }

    /**
     * @inheritDoc
     */
    public function setAcknowledgeId(string $ackId)
    {
        $this->ackId = $ackId;
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
}
