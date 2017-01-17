<?php

namespace Flip\Rule\Rule;

use Flip\Service\FlipUserServiceInterface;

/**
 * Satisfied when a flip is earned exactly X number of times
 */
class EarnedFlipXTimes extends AbstractEarnedFlipRule
{
    /**
     * @var FlipUserServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $expectedFlip;

    /**
     * @var string
     */
    protected $userProvider;

    /**
     * @var number
     */
    protected $timesEarned;

    /**
     * EarnedFlipXTimes constructor.
     *
     * @param FlipUserServiceInterface $service
     * @param string $expectedFlip
     * @param string $userProvider
     * @param int $timesEarned
     */
    public function __construct(
        FlipUserServiceInterface $service,
        string $expectedFlip,
        string $userProvider,
        int $timesEarned
    ) {
        $this->service      = $service;
        $this->expectedFlip = $expectedFlip;
        $this->userProvider = $userProvider;
        $this->timesEarned  = abs($timesEarned);
    }

    /**
     * @inheritDoc
     */
    protected function getFlipId(): string
    {
        return $this->expectedFlip;
    }

    /**
     * @inheritDoc
     */
    protected function getUserProviderName(): string
    {
        return $this->userProvider;
    }

    /**
     * @inheritDoc
     */
    protected function getFlipUserService(): FlipUserServiceInterface
    {
        return $this->service;
    }

    /**
     * @inheritDoc
     */
    protected function isSatisfied(int $timesEarned): bool
    {
        return $timesEarned === $this->timesEarned;
    }
}
