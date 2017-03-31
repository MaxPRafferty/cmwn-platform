<?php

namespace Flip\Validator;

use Flip\FlipInterface;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Uri;
use Zend\Validator\ValidatorInterface;

/**
 * Validates the flip uris
 */
class UriValidator extends AbstractValidator implements ValidatorInterface
{
    const MISSING_KEY    = 'missingKey';
    const INVALID_URI    = 'invalidUri';
    const INVALID_TYPE   = 'invalidType';
    const INVALID_SCHEME = 'invalidScheme';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::MISSING_KEY    => 'Missing keys or invalid key set expected: [%value%]',
        self::INVALID_URI    => '%value% is not a valid URI',
        self::INVALID_TYPE   => 'Value passed in is not a key value pair',
        self::INVALID_SCHEME => 'Uri must be from a secure domain',
    ];

    /**
     * @var array
     */
    protected $requiredKeys = [
        FlipInterface::IMAGE_EARNED,
        FlipInterface::IMAGE_UNEARNED,
        FlipInterface::IMAGE_STATIC,
        FlipInterface::IMAGE_COIN,
        FlipInterface::IMAGE_DEFAULT,
    ];

    /**
     * @var Uri
     */
    protected $uriValidator;

    /**
     * @inheritDoc
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->uriValidator = new Uri(['allowRelative' => false, 'allowAbsolute' => true]);
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        if (!is_array($value)) {
            $this->error(self::INVALID_TYPE);

            return false;
        }

        if (array_keys($value) !== $this->requiredKeys) {
            $this->error(self::MISSING_KEY, implode(', ', $this->requiredKeys));

            return false;
        }

        $validUri = true;
        array_walk($value, function ($value) use (&$validUri) {
            if (!$this->uriValidator->isValid($value)) {
                $this->error(self::INVALID_URI, $value);
                $validUri = false;

                return;
            }

            if ('https' !== $this->uriValidator->getUriHandler()->getScheme()) {
                $this->error(self::INVALID_SCHEME, $value);
                $validUri = false;
            }
        });

        return $validUri;
    }
}
