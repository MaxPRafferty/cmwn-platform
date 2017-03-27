<?php

namespace Game\Rule\Rule;

use Application\Exception\NotFoundException;
use Game\Game;
use Game\Service\UserGameServiceInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Security\Rule\Provider\ActiveUserProvider;
use User\UserInterface;

/**
 * Rule is satisfied when user can play a game
 */
class UserCanPlayGame implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var UserGameServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $gameId;

    /**
     * @var string
     */
    protected $userIdProvider;

    /**
     * UserGamePlayGame constructor.
     *
     * @param UserGameServiceInterface $service
     * @param string $gameId
     * @param string $userIdProvider
     */
    public function __construct(
        UserGameServiceInterface $service,
        string $gameId,
        string $userIdProvider = ActiveUserProvider::PROVIDER_NAME
    ) {
        $this->service        = $service;
        $this->gameId         = $gameId;
        $this->userIdProvider = $userIdProvider;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $user = $item->getParam($this->userIdProvider);
        static::checkValueType($user, UserInterface::class);

        try {
            $game = new Game();
            $game->setGameId($this->gameId);
            $this->service->fetchGameForUser($user, $game);
            $this->timesSatisfied++;
        } catch (NotFoundException $notFoundException) {
            // noop
        }

        return $this->timesSatisfied() == 1;
    }
}
