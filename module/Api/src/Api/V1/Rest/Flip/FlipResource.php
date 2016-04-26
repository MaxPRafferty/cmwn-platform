<?php

namespace Api\V1\Rest\Flip;

use Flip\Service\FlipServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FlipResource
 */
class FlipResource extends AbstractResourceListener
{
    /**
     * @var FlipServiceInterface
     */
    protected $flipService;

    /**
     * FlipResource constructor.
     * @param FlipServiceInterface $flipService
     */
    public function __construct(FlipServiceInterface $flipService)
    {
        $this->flipService = $flipService;
    }


    /**
     * Fetch a resource
     *
     * @param  mixed $flipId
     * @return ApiProblem|FlipEntity
     */
    public function fetch($flipId)
    {
        return new FlipEntity($this->flipService->fetchFlipById($flipId)->getArrayCopy());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new FlipCollection($this->flipService->fetchAll(null, new FlipEntity()));
    }
}
