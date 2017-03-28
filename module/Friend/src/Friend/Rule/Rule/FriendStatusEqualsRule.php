<?php

namespace Friend\Rule\Rule;

use Friend\FriendInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;

/**
 * Compares friend status to provided status
 */
class FriendStatusEqualsRule implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $friendStatus;

    /**
     * @var string
     */
    protected $friendProvider;

    /**
     * SkribbleStatusEqualsRule constructor.
     * @param string $friendStatus
     * @param string $providerName
     */
    public function __construct(
        string $providerName = 'friend',
        string $friendStatus = FriendInterface::FRIEND
    ) {
        $this->friendStatus = $friendStatus;
        $this->friendProvider = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $friend = $item->getParam($this->friendProvider);
        static::checkValueType($friend, FriendInterface::class);

        if ($friend->getFriendStatus() === $this->friendStatus) {
            return true;
        }

        return false;
    }
}
