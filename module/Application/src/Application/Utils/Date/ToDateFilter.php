<?php

namespace Application\Utils\Date;

use Zend\Filter\AbstractFilter;
use Zend\Filter\FilterInterface;

/**
 * Class TodayDateFilter
 *
 * Converts a value into a DateTimeObject as a filter
 */
class ToDateFilter extends AbstractFilter implements FilterInterface
{
    /**
     * @var string Valid date string
     */
    protected $defaultStartDate = 'now';

    /**
     * ToDateFilter constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Sets the default date to use when transforming an empty value
     *
     * @param $value
     */
    public function setDefaultStartDate($value)
    {
        $this->defaultStartDate = $value;
    }

    /**
     * @inheritDoc
     * @return \DateTime
     */
    public function filter($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        return DateTimeFactory::factory(empty($value) ? $this->defaultStartDate : $value);
    }
}
