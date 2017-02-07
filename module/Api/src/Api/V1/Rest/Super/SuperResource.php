<?php

namespace Api\V1\Rest\Super;

use Api\V1\Rest\User\UserEntity;
use Security\SecurityUser;
use Security\Service\SecurityServiceInterface;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;
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
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * SuperResource constructor.
     * @param SecurityServiceInterface $securityService
     * @param UserServiceInterface $userService
     */
    public function __construct(SecurityServiceInterface $securityService, UserServiceInterface $userService)
    {
        $this->securityService = $securityService;
        $this->userService = $userService;
    }

    /**
     * @param array $params
     * @return SuperCollection
     */
    public function fetchAll($params = [])
    {
        $where['u.super'] = 1;
        $superUsers = $this->userService->fetchAll($where, true, new UserEntity());

        return new SuperCollection($superUsers);
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

    /**
     * @param mixed $data
     * @return mixed|\ZF\ApiProblem\ApiProblem
     */
    public function create($data)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $user = $this->userService->fetchUser($userId);

        //@todo create a new validator to make this check
        if ($user->getType()!== UserInterface::TYPE_ADULT) {
            return new ApiProblem(403, 'Action forbidden for non adult users');
        }

        $this->securityService->setSuper($userId);

        return new UserEntity($user->getArrayCopy());
    }

    public function delete($userId)
    {
        $this->securityService->setSuper($userId, false);

        return new ApiProblem(200, 'This user is now not a super.');
    }
}
