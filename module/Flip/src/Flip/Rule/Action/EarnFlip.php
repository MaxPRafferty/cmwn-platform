<?php

namespace Flip\Rule\Action;

use Flip\Service\FlipUserServiceInterface;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;

/**
 * Action that will earn a flip for a user
 */
class EarnFlip implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var FlipUserServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $flipId;

    /**
     * @var string
     */
    protected $userProvider;

    /**
     * EarnFlip constructor.
     *
     * @param FlipUserServiceInterface $service
     * @param string $flipId
     * @param string $userProvider
     */
    public function __construct(
        FlipUserServiceInterface $service,
        string $flipId,
        string $userProvider
    ) {
        $this->service      = $service;
        $this->flipId       = $flipId;
        $this->userProvider = $userProvider;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        /** @var UserInterface $user */
        $user = $item->getParam($this->userProvider);
        $this->service->attachFlipToUser($user, $this->flipId);
    }
}
