<?php

namespace Game\Rule\Action;

use Game\Game;
use Game\Service\UserGameServiceInterface;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use Security\Rule\Provider\ActiveUserProvider;
use User\UserInterface;

/**
 * Adds a game to a user
 */
class AddGameToUserAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var UserGameServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $userProvider;

    /**
     * @var string
     */
    protected $gameId;

    /**
     * AddGameToUserAction constructor.
     *
     * @param UserGameServiceInterface $service
     * @param string $gameId
     * @param string $userProvider
     */
    public function __construct(
        UserGameServiceInterface $service,
        string $gameId,
        string $userProvider = ActiveUserProvider::PROVIDER_NAME
    ) {

        $this->service      = $service;
        $this->userProvider = $userProvider;
        $this->gameId       = $gameId;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        $game = new Game();
        $game->setGameId($this->gameId);

        $user = $item->getParam($this->userProvider);
        static::checkValueType($user, UserInterface::class);

        $this->service->attachGameToUser($user, $game);
    }
}
