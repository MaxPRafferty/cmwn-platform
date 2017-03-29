<?php

namespace Game\Rule\Rule;

use Application\Exception\NotFoundException;
use Game\Service\GameServiceInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;

/**
 * Rule is satisfied if a game exists
 */
class GameExists implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var GameServiceInterface
     */
    protected $gameService;

    /**
     * @var string
     */
    protected $gameId;

    /**
     * GameExists constructor.
     *
     * @param GameServiceInterface $gameService
     * @param string $gameId
     */
    public function __construct(GameServiceInterface $gameService, string $gameId)
    {
        $this->gameService = $gameService;
        $this->gameId      = $gameId;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        try {
            $this->gameService->fetchGame($this->gameId);
            $this->timesSatisfied++;
        } catch (NotFoundException $exception) {
            // noop
        }

        return $this->timesSatisfied() === 1;
    }
}
