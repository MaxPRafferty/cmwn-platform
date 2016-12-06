<?php

namespace Security;

use Application\Utils\Date\DateTimeFactory;
use Group\GroupInterface;

use User\User;

/**
 * Class SecurityUser
 *
 * A security user, is a user that is logged in.   This user can be saved to the database
 * however not all the security will be saved.  To Save the password, code and super flag,
 * use the security service
 *
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
     * @var \DateTime
     */
    protected $codeExpires;

    /**
     * @var \DateTime
     */
    protected $codeStarts;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $super = false;

    /**
     * @var array
     */
    protected $groupTypes = [];

    /**
     * @var string
     */
    protected $role = 'guest';

    /**
     * @var string
     */
    protected $normalizedUsername;

    /**
     * Sets the data for the user
     *
     * Also sets the code, password and super flag
     *
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'code'         => null,
            'code_expires' => null,
            'code_starts'  => null,
            'password'     => null,
            'super'        => false,
        ];

        $array = array_merge($defaults, $array);
        parent::exchangeArray($array);

        $this->password    = $array['password'];
        $this->code        = $array['code'];
        $this->codeExpires = DateTimeFactory::factory($array['code_expires']);
        $this->codeStarts  = DateTimeFactory::factory($array['code_starts']);
        $this->super       = (bool)$array['super'];
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
     *
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
     *
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
     *
     * @return string
     */
    public function compareCode($code)
    {
        if ($code !== $this->code) {
            return static::CODE_INVALID;
        }

        if ($this->codeExpires === null) {
            return static::CODE_INVALID;
        }

        $now = DateTimeFactory::factory('now');
        if ($this->codeExpires === null || $now->getTimestamp() > $this->codeExpires->getTimestamp()) {
            return static::CODE_EXPIRED;
        }

        // Since we are adding the code starts we only check the start date if set
        if (($this->codeStarts !== null) &&
            ($now->getTimestamp() < $this->codeStarts->getTimestamp())
        ) {
            return static::CODE_EXPIRED;
        }

        return static::CODE_VALID;
    }

    /**
     * Gets the temp code for the user
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Tests if the user is a super admin or not
     *
     * @return bool
     */
    public function isSuper()
    {
        return $this->super;
    }

    /**
     * @param $type |GroupInterface
     *
     * @return $this
     */
    public function addGroupType($type)
    {
        $type                    = $type instanceof GroupInterface ? $type->getType() : $type;
        $this->groupTypes[$type] = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        if ($this->isSuper()) {
            return 'super';
        }

        return $this->role;
    }

    /**
     * @param $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return array
     */
    public function getGroupTypes()
    {
        return $this->groupTypes;
    }

    /**
     * @return string
     */
    public function getNormalizedUsername()
    {
        return $this->normalizedUsername;
    }

    /**
     * @param $normalizedUsername
     */
    public function setNormalizedUsername($normalizedUsername)
    {
        $this->normalizedUsername = $normalizedUsername;
    }
}
