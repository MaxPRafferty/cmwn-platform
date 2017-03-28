<?php

namespace Game\Rule\Rule;

use Game\GameInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;

/**
 * This checks if a game is coming soon or not
 */
class GameComingSoonRule implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var String
     */
    protected $gameProvider;

    /**
     * GameComingSoonRule constructor.
     * @param string $providerName
     */
    public function __construct($providerName = 'game')
    {
        $this->gameProvider = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $game = $item->getParam($this->gameProvider);
        static::checkValueType($game, GameInterface::class);

        if (!$game->isComingSoon()) {
            return true;
        }

        return false;
    }
}
