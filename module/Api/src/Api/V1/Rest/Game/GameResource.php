<?php
namespace Api\V1\Rest\Game;

use Game\Service\GameServiceInterface;
use Zend\Paginator\Adapter\DbSelect;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GameResource
 * @package Api\V1\Rest\Game
 */
class GameResource extends AbstractResourceListener
{
    /**
     * @var GameServiceInterface
     */
    protected $service;

    /**
     * UserResource constructor.
     * @param GameServiceInterface $service
     */
    public function __construct(GameServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        /** @var DbSelect $games */
        $games = $this->service->fetchAll(null, true, new GameEntity());
        return new GameCollection($games);
    }
}
