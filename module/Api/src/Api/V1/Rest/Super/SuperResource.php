<?php

namespace Api\V1\Rest\Super;

use Api\V1\Rest\User\UserEntity;
use Security\SecurityUser;
use Security\Service\SecurityServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class SuperResource
 * @package Api\V1\Rest
 */
class SuperResource extends AbstractResourceListener
{
    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * SuperResource constructor.
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(SecurityServiceInterface $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @inheritdoc
     */
    public function fetch($id)
    {
        /**@var SecurityUser $user*/
        $user = $this->securityService->fetchUserByUserId($id);
        if ($user->isSuper()) {
            return new UserEntity($user->getArrayCopy());
        }

        return new ApiProblem(404, 'Not Found');
    }
}
