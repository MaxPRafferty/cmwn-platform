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
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        return new ApiProblem(405, 'The POST method has not been defined');
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $gameId
     * @return ApiProblem|mixed
     */
    public function fetch($gameId)
    {
        return new GameEntity($this->service->fetchGame($gameId)->getArrayCopy());
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
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $gameId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($gameId, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $gameId
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($gameId, $data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
