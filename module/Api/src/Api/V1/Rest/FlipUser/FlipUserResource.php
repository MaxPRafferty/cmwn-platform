<?php

namespace Api\V1\Rest\FlipUser;

use Flip\Service\FlipUserServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FlipUserResource
 */
class FlipUserResource extends AbstractResourceListener
{
    /**
     * @var FlipUserServiceInterface
     */
    protected $flipUserService;

    /**
     * FlipUserResource constructor.
     * @param FlipUserServiceInterface $flipUserService
     */
    public function __construct(FlipUserServiceInterface $flipUserService)
    {
        $this->flipUserService = $flipUserService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $user = $this->getEvent()->getRouteParam('user');
        $flipId = $this->getInputFilter()->getValue('flip_id');

        $flip = $this->flipUserService->attachFlipToUser($user, $flipId);
        return new FlipUserEntity($flip->getArrayCopy());
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $flipId
     * @return ApiProblem|mixed
     */
    public function fetch($flipId)
    {
        $user = $this->getEvent()->getRouteParam('user');
        return new FlipUserCollection(
            $this->flipUserService->fetchEarnedFlipsForUser($user, ['uf.flip_id' => $flipId], new FlipUserEntity())
        );
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $user = $this->getEvent()->getRouteParam('user');
        return new FlipUserCollection(
            $this->flipUserService->fetchEarnedFlipsForUser($user, null, new FlipUserEntity())
        );
    }
}
