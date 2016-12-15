<?php

namespace Application\Utils\Date;

use Zend\Validator\Date;
use Zend\Validator\ValidatorInterface;

/**
 * Class DateBetweenValidator
 */
class DateGreaterThanValidator extends Date implements ValidatorInterface
{
    /**#@+
     * Validity constants
     *
     * @var string
     */
    const DATE_TOO_SOON = 'dateTooSoon';
    /**#@-*/

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::DATE_TOO_SOON => "The date '%value%' is BEFORE %start_date%",
    ];

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $startDateString;

    /**
     * @var array
     */
    protected $messageVariables = [
        'start_date' => 'startDateString',
    ];

    /**
     * DateBetweenValidator constructor.
     *
     * @param array|string|\Traversable $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
        if (null === $this->startDate) {
            throw new \InvalidArgumentException('Date Between Validator needs a start and end date');
        }

        $this->startDateString = $this->startDate->format(\DateTime::RFC822);
    }

    /**
     * @param $value
     */
    public function setStartDate($value)
    {
        $this->startDate = DateTimeFactory::factory($value);
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        $value = DateTimeFactory::factory($value);
        $ok    = parent::isValid($value);

        if ($ok && $value->getTimestamp() < $this->startDate->getTimestamp()) {
            $this->error(static::DATE_TOO_SOON);
            $ok = false;
        }

        return $ok;
    }
}
