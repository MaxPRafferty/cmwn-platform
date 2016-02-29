<?php

namespace Api\V1\Rest\Game;

use Game\Service\GameServiceInterface;

/**
 * Class GameResourceFactory
 * @package Api\V1\Rest\Game
 */
class GameResourceFactory
{
    /**
     * @param $services
     * @return GameResource
     */
    public function __invoke($services)
    {
        /** @var GameServiceInterface $userService */
        $gameService = $services->get('Game\Service');
        return new GameResource($gameService);
    }
}
