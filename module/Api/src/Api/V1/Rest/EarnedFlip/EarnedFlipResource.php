<?php

namespace Api\V1\Rest\EarnedFlip;

use Flip\Service\FlipUserServiceInterface;
use User\Service\UserServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FlipUserResource
 */
class EarnedFlipResource extends AbstractResourceListener
{
    /**
     * @var FlipUserServiceInterface
     */
    protected $flipUserService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * FlipUserResource constructor.
     *
     * @param FlipUserServiceInterface $flipUserService
     */
    public function __construct(
        FlipUserServiceInterface $flipUserService,
        UserServiceInterface $userService
    ) {
        $this->flipUserService = $flipUserService;
        $this->userService     = $userService;
    }

    /**
     * Gets the user from the route
     *
     * @return \User\UserInterface
     */
    protected function getUserFromRoute()
    {
        return $this->userService->fetchUser(
            $this->getEvent()->getRouteParam('user', '')
        );
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $flipId = $this->getInputFilter()->getValue('flip_id');

        $this->flipUserService->attachFlipToUser(
            $this->getUserFromRoute(),
            $flipId
        );

        return $this->fetch($flipId);
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $flipId
     *
     * @return ApiProblem|mixed
     */
    public function fetch($flipId)
    {
        return new EarnedFlipCollection(
            $this->flipUserService->fetchEarnedFlipsForUser(
                $this->getUserFromRoute(),
                ['flip_id' => $flipId],
                new EarnedFlipEntity()
            )
        );
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new EarnedFlipCollection(
            $this->flipUserService->fetchEarnedFlipsForUser(
                $this->getUserFromRoute(),
                null,
                new EarnedFlipEntity()
            )
        );
    }
}
