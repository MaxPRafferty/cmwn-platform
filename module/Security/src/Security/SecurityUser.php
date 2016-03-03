<?php

namespace Security;

use Application\Utils\Date\DateTimeFactory;

class SecurityUser
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


    public function __construct(array $options)
    {
        $defaults = [
            'user_id'      => null,
            'username'     => null,
            'email'        => null,
            'code'         => null,
            'code_expires' => null,
            'password'     => null
        ];

        $array = array_merge($defaults, $options);

        $this->userId      = $array['user_id'];
        $this->userName    = $array['username'];
        $this->email       = $array['email'];
        $this->code        = $array['code'];
        $this->codeExpires = DateTimeFactory::factory($array['code_expires']);
        $this->password    = $array['password'];
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
