<?php

namespace Friend;

use Application\Exception\NotFoundException;
use Friend\Service\FriendServiceInterface;
use User\Service\UserServiceInterface;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Zend\Validator\ValidatorInterface;

/**
 * Class AttachFriendValidator
 */
class AttachFriendValidator extends AbstractValidator implements ValidatorInterface
{
    const INVALID_USER    = 'invalidUser';
    const INVALID_FRIEND  = 'invalidFriend';
    const ALREADY_FRIENDS = 'alreadyFriends';

    /**
     * @var string[]
     */
    protected $messageTemplates = [
        self::INVALID_USER    => 'Invalid user_id "%value%"',
        self::INVALID_FRIEND  => 'Invalid friend_id "%value%"',
        self::ALREADY_FRIENDS => 'Users are already Friends',
    ];

    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * AttachFriendValidator constructor.
     *
     * @param FriendServiceInterface $friendService
     * @param UserServiceInterface $userService
     * @param null $options
     */
    public function __construct(
        FriendServiceInterface $friendService,
        UserServiceInterface $userService,
        $options = null
    ) {
        $this->friendService = $friendService;
        $this->userService   = $userService;
        parent::__construct($options);
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @param  array $context
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value, $context = [])
    {
        $userId = isset($context['user_id']) ? $context['user_id'] : null;
        try {
            $user = $this->userService->fetchUser($userId);
        } catch (NotFoundException $notFound) {
            $this->error(static::INVALID_USER);
            return false;
        }

        try {
            $friend = $this->userService->fetchUser($value);
        } catch (NotFoundException $notFound) {
            $this->error(static::INVALID_FRIEND);
            return false;
        }

        // check if users are friends already
        try {
            $this->friendService->fetchFriendForUser($user, $friend);
            $this->error(static::ALREADY_FRIENDS);
            return false;
        } catch (NotFriendsException $notFriends) {
            // this is good
        }

        return true;
    }
}
