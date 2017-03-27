<?php

namespace Group;

use User\Service\UserServiceInterface;
use Zend\Validator\AbstractValidator;

/**
 * Validates the role provided against the roles in the config
 */
class RoleValidator extends AbstractValidator
{
    const INVALID_ROLE = "invalidRoleProvided";

    protected $messageTemplates = [
        self::INVALID_ROLE => "Invalid role provider for user in a group %value%",
    ];

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var array
     */
    protected $roles;

    /**
     * RoleValidator constructor.
     * @param UserServiceInterface $userService
     * @param array $config
     */
    public function __construct(UserServiceInterface $userService, $config = [])
    {
        parent::__construct();
        $this->userService = $userService;
        $this->roles = $config['cmwn-roles']['roles'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function isValid($value, $context = [])
    {
        $userId = $context['user_id'] ?? null;

        $user = $this->userService->fetchUser($userId);
        $givenRole = $value . '.' . strtolower($user->getType());

        $return = in_array($givenRole, array_keys($this->roles)) ?? false;

        if (!$return) {
            $this->error(static::INVALID_ROLE);
        }

        return $return;
    }
}
