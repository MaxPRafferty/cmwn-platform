<?php

namespace Api\V1\Rest\SuperFlag;

use Api\V1\Rest\User\UserEntity;
use Security\Service\SecurityServiceInterface;
use User\Service\UserServiceInterface;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SuperFlagResource
 * @package Api\V1\Rest\SuperFlag
 */
class SuperFlagResource extends AbstractResourceListener
{
    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * SuperFlagResource constructor.
     * @param SecurityServiceInterface $securityService
     * @param UserServiceInterface $userService
     */
    public function __construct(SecurityServiceInterface $securityService, UserServiceInterface $userService)
    {
        $this->securityService = $securityService;
        $this->userService = $userService;
    }

    /**
     * @param mixed $data
     * @return mixed|\ZF\ApiProblem\ApiProblem
     */
    public function create($data)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $user = $this->userService->fetchUser($userId, null);

        //@todo create a new validator to make this check
        if ($user->getType()!== UserInterface::TYPE_ADULT) {
            return new ApiProblem(403, 'Action forbidden for non adult users');
        }

        $data = (array) $data;
        $super = $data['super'];

        $this->securityService->setSuper($userId, $super);

        return new UserEntity($user->getArrayCopy());
    }
}
