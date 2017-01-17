<?php

namespace Flip\Rule\Rule;

use Flip\Service\FlipUserServiceInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use User\UserInterface;

/**
 * Abstract class that helps rules that deals with the number of times a flip is earned
 */
abstract class AbstractEarnedFlipRule implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        /** @var UserInterface $user */
        $user = $item->getParam($this->getUserProviderName());
        static::checkValueType($user, UserInterface::class);

        $timesEarned = $this->getFlipUserService()->fetchFlipsForUser(
            $user,
            $this->getFlipId()
        );

        if ($this->isSatisfied($timesEarned->count())) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }

    /**
     * Children return the id of the flip expected
     *
     * @return string
     */
    abstract protected function getFlipId(): string;

    /**
     * Children return the name of the provider param
     *
     * @return string
     */
    abstract protected function getUserProviderName(): string;

    /**
     * Children return the FlipUserService
     *
     * @return FlipUserServiceInterface
     */
    abstract protected function getFlipUserService(): FlipUserServiceInterface;

    /**
     * Children are passed the number of times a flip was earned, then decide if the rule is satisfied
     *
     * @param int $timesEarned
     *
     * @return bool
     */
    abstract protected function isSatisfied(int $timesEarned): bool;
}
