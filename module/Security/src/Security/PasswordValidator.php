<?php

namespace Security;

use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Exception;
use Zend\Validator\Regex;
use Zend\Validator\ValidatorInterface;

class PasswordValidator extends Regex implements ValidatorInterface
{
    /**
     * @var array
     */
    protected $messageTemplates = [
        self::INVALID   => "Password must be at least 8 characters with one of them being a number",
        self::NOT_MATCH => "Password must be at least 8 characters with one of them being a number",
        self::ERROROUS  => "There was an internal error while using the pattern '%pattern%'",
    ];

    public function __construct()
    {
        parent::__construct($this->getPattern());
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return '/^([a-zA-Z])[a-zA-Z0-9]{7,}$/';
    }
}
