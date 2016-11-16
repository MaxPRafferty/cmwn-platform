<?php
namespace Api\V1\Rest\Game;

use Game\Game;
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

    /**
     * @inheritdoc
     */
    public function fetch($gameId)
    {
        $game = $this->service->fetchGame($gameId);

        return new GameEntity($game->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $game = new Game($this->getInputFilter()->getValues());
        $this->service->createGame($game);
        return new GameEntity($game->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function update($gameId, $data)
    {
        $game = $this->service->fetchGame($gameId);
        $data = $this->getInputFilter()->getValues();

        $saveGame = new Game(array_merge($game->getArrayCopy(), $data));
        $this->service->saveGame($saveGame);

        return $saveGame;
    }

    /**
     * @inheritdoc
     */
    public function delete($gameId)
    {
        $game = $this->fetch($gameId);
        $this->service->deleteGame($game);
        return new ApiProblem(200, 'Game deleted', 'Ok');
    }
}
