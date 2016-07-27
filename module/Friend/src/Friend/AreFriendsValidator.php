<?php

namespace Friend;

use Friend\Service\FriendServiceInterface;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\AbstractValidator;

/**
 * Class AreFriendsValidator
 */
class AreFriendsValidator extends AbstractValidator implements ValidatorInterface
{
    const NO_VALUE = 'noFriendId';
    const FATAL_ERROR = 'unknown';

    /**
     * @var string[]
     */
    protected $messageTemplates = [
        self::NO_VALUE   => 'Rules must be an array or object',
        self::FATAL_ERROR => 'Unknown error occurred contact support' // TODO Better message
    ];


    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * AreFriendsValidator constructor.
     *
     * @param FriendServiceInterface $options
     */
    public function __construct(FriendServiceInterface $friendService, $options)
    {
        $this->friendService = $friendService;
        parent::__construct($options);
    }

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        if (empty($value)) {
        }

        return true;
    }
}
