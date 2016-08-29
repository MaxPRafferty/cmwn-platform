<?php

namespace Api\V1\Rest\Feed;

use Api\V1\Rest\Game\GameResource;
use Game\Service\GameService;
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
     * @param array $params
     * @return FeedCollection
     */
    public function fetchAll($params = [])
    {
        /**@var DbSelect $games*/
        $feeds = $this->gameService->fetchAll(null, true, new FeedEntity());

        return new FeedCollection($feeds);
    }
}
