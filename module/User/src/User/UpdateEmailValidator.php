<?php

namespace User;

use Application\Exception\NotFoundException;
use User\Service\UserService;
use Zend\Validator\AbstractValidator;

/**
 * Class UpdateUsernameValidator
 * @package User
 */
class UpdateEmailValidator extends AbstractValidator
{
    const ALREADY_TAKEN = "emailAlreadyTaken";

    /**
     * @var UserService
     */
    protected $userService;

    protected $messageTemplates = [
        self::ALREADY_TAKEN => "Email %value% already taken, choose a new one",
    ];

    /**
     * UpdateUsernameValidator constructor.
     * @param array|null|\Traversable $options
     * @param UserService $userService
     */
    public function __construct($options, $userService)
    {
        parent::__construct($options);
        $this->userService = $userService;
    }

    /**
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = [])
    {
        $userId = (isset($context['user_id']))? $context['user_id']:null;

        try {
            /**@var UserInterface $user*/
            $user = $this->userService->fetchUserByEmail($value);
        } catch (NotFoundException $notFound) {
            return true;
        }

        if ($userId===null || $user->getUserId()!==$userId) {
            $this->error(static::ALREADY_TAKEN);
            return false;
        }

        return true;
    }
}
