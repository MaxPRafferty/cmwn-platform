<?php

namespace Security;

use Group\GroupInterface;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use User\User;
use User\UserInterface;

/**
 * A security user, is a user that is logged in.   This user can be saved to the database
 * however not all the security will be saved.  To Save the password, code and super flag,
 * use the security service
 */
class SecurityUser extends User implements SecurityUserInterface
{
    /**
     * @var string
     */
    protected $userName;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string
     */
    protected $code;

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
    protected $role;

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): UserInterface
    {
        $defaults = [
            'code'     => null,
            'password' => null,
            'super'    => false,
        ];

        $array = array_merge($defaults, $array);
        parent::exchangeArray($array);

        $this->password = $array['password'];
        $this->code     = $array['code'];
        $this->super    = (bool)$array['super'];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function comparePassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @inheritdoc
     */
    public function compareCode(string $code): string
    {
        if (null === $this->code) {
            return static::CODE_INVALID;
        }
        try {
            $compareToken = (new Parser())->parse($this->code);
            $validator    = new ValidationData();
            $validator->setId($code);

            return $compareToken->validate($validator)
                ? static::CODE_VALID
                : static::CODE_EXPIRED;
        } catch (\Exception $jwtException) {
        }

        return static::CODE_INVALID;
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return (string) $this->code;
    }

    /**
     * @inheritdoc
     */
    public function isSuper(): bool
    {
        return $this->super;
    }

    /**
     * @inheritdoc
     */
    public function getRole(): string
    {
        if ($this->isSuper()) {
            return 'super';
        }

        if (null === $this->role) {
            return 'me.' . strtolower($this->getType());
        }

        return $this->role;
    }

    /**
     * @inheritdoc
     */
    public function setRole(string $role)
    {
        $this->role = $role;
    }

    /**
     * @param $type
     *
     * @return $this
     * @deprecated
     */
    public function addGroupType($type)
    {
        $type                    = $type instanceof GroupInterface ? $type->getType() : $type;
        $this->groupTypes[$type] = $type;

        return $this;
    }

    /**
     * @return array
     * @deprecated
     */
    public function getGroupTypes()
    {
        return $this->groupTypes;
    }
}
