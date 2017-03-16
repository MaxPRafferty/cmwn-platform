<?php

namespace Address;

use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\Exceptions\ObjectNotFoundException;
use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

/**
 * Validator to validate country and state codes passed in as values for address
 * @SuppressWarnings(CyclomaticComplexity)
 */
class CountryStateValidator extends AbstractValidator implements ValidatorInterface
{
    const INVALID_COUNTRY = 'invalidCountry';
    const INVALID_ADMINISTRATIVE_AREA = 'invalidAdministrativeArea';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID_COUNTRY => 'Invalid value given for country code',
        self::INVALID_ADMINISTRATIVE_AREA => 'Invalid valu given for Administrative area',
    ];

    /**
     * @var string
     */
    protected $fieldName;

    /**
     * AddressValidator constructor.
     * @param array|null|\Traversable $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->fieldName = $options['fieldName'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function isValid($value, array $context = null)
    {
        $countryCode = $this->fieldName === 'country' ? $value :
            ($this->fieldName === 'administrative_area' && $context['country'] ? $context['country'] : '');

        try {
            $country = Country::build($countryCode);
        } catch (ObjectNotFoundException $e) {
            $this->error(static::INVALID_COUNTRY);
            return false;
        }

        if ($this->fieldName === 'administrative_area') {
            $state = $country->getStates()->findOne(['isoCode' => $value]);
            if (!$state) {
                $this->error(static::INVALID_ADMINISTRATIVE_AREA);
                return false;
            }
        }

        return true;
    }
}
