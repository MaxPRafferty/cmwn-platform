<?php

namespace Api\V1\Rest\Feed;

use Api\Links\GameLink;
use Api\V1\Rest\Game\GameResource;
use Game\Game;
use Game\Service\GameService;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Adapter\DbSelect;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FeedResource
 */
class FeedResource extends AbstractResourceListener
{
    /**
     * @var GameResource
     */
    protected $gameService;

    /**
     * FeedResource constructor.
     * @param GameService $gameService
     */
    public function __construct($gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @param $games
     * @return array
     */
    protected function createFeedFromGames($games)
    {
        $feeds = [];
        /**@var \Game\GameInterface $game*/
        foreach ($games as $game) {
            $feedData = [
                'type' => 'game',
            ];

            $gameData = $game->getArrayCopy();
            $gameId = $game->getGameId();
            $feedData['header'] = $game->getTitle();
            $feedData['message'] = $game->getDescription();
            $feedData['created'] = isset($gameData['created']) ? $gameData['created'] : null;
            $feedData['link'] = new GameLink($gameId);
            $feeds[] = new FeedEntity($feedData);
        }
        return $feeds;
    }

    /**
     * @param array $params
     * @return FeedCollection
     */
    public function fetchAll($params = [])
    {
        /**@var DbSelect $games*/
        $games = $this->gameService->fetchAll(null, true, new Game());

        $games = $games->getItems(0, count($games));
        $feeds = $this->createFeedFromGames($games);

        return new FeedCollection(new ArrayAdapter($feeds));
    }
}
