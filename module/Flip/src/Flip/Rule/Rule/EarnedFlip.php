<?php

namespace Flip\Rule\Rule;

use Flip\Service\FlipUserServiceInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;

/**
 * A Rule that is satisfied if a user has earned a flip at least once
 */
class EarnedFlip extends AbstractEarnedFlipRule
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

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
     * EarnedFlip constructor.
     *
     * @param FlipUserServiceInterface $service
     * @param string $expectedFlip
     * @param string $userProvider
     */
    public function __construct(
        FlipUserServiceInterface $service,
        string $expectedFlip,
        string $userProvider
    ) {
        $this->service      = $service;
        $this->expectedFlip = $expectedFlip;
        $this->userProvider = $userProvider;
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
        return $this->expectedFlip;
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
        return $timesEarned > 0;
    }
}
