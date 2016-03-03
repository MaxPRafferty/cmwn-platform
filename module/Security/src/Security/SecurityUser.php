<?php

namespace Security;

use Application\Utils\Date\DateTimeFactory;
use User\User;

/**
 * Class SecurityUser
 * @package Security
 */
class SecurityUser extends User
{
    const CODE_EXPIRED = 'Expired';
    const CODE_INVALID = 'Invalid';
    const CODE_VALID   = 'Valid';

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var int
     */
    protected $codeExpires;

    /**
     * @var string
     */
    protected $type;


    public function exchangeArray(array $array)
    {
        $defaults = [
            'code'         => null,
            'code_expires' => null,
            'password'     => null,
        ];

        $array = array_merge($defaults, $array);
        parent::exchangeArray($array);

        $this->password    = $array['password'];
        $this->code        = $array['code'];
        $this->codeExpires = DateTimeFactory::factory($array['code_expires']);
    }

    /**
     * Gets the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Verifies the password
     *
     * @param $password
     * @return bool
     */
    public function comparePassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Compare string to a code
     *
     * @param $code
     * @return string
     */
    public function compareCode($code)
    {
        if ($code !== $this->code) {
            return static::CODE_INVALID;
        }

        $now = DateTimeFactory::factory('now');
        if ($this->codeExpires === null || $now->getTimestamp() > $this->codeExpires->getTimestamp()) {
            return static::CODE_EXPIRED;
        }

        return static::CODE_VALID;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
