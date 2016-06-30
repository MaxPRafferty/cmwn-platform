<?php

namespace Flip;

use Application\Utils\Date\DateTimeFactory;

/**
 * Class EarnedFlip
 */
class EarnedFlip extends Flip implements EarnedFlipInterface
{
    /**
     * @var \DateTime
     */
    protected $earned;

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $earned          = isset($array['earned']) ? DateTimeFactory::factory($array['earned']) : null;
        $array['earned'] = $earned;
        parent::exchangeArray($array);
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $return           = parent::getArrayCopy();
        $return['earned'] = $this->getEarned() !== null ? $this->getEarned()->format(\DateTime::ISO8601) : null;
        return $return;
    }

    /**
     * @return \DateTime
     */
    public function getEarned()
    {
        return $this->earned;
    }

    /**
     * @param \DateTime $earned
     */
    public function setEarned(\DateTime $earned = null)
    {
        $this->earned = $earned;
    }
}
