<?php

namespace Api\V1\Rest\UserName;

use Api\V1\Rest\User\MeEntity;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\SecurityUser;
use User\Child;
use User\Service\StaticNameService;
use User\Service\UserServiceInterface;
use User\UserInterface;
use User\UserName;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class UserNameResource extends AbstractResourceListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * UserNameResource constructor.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|MeEntity
     */
    public function create($data)
    {
        /** @var SecurityUser $loggedInUser */
        $loggedInUser = $this->getAuthenticationService()->getIdentity();

        if ($loggedInUser->getType() !== UserInterface::TYPE_CHILD) {
            return new ApiProblem(405, 'The POST method has not been defined');
        }

        $userName     = $this->getInputFilter()->getValue('user_name');
        list($leftName, $rightName) = explode(UserName::SEPARATOR, $userName);

        $user = new Child($loggedInUser->getArrayCopy());
        $user->setGeneratedName(new UserName($leftName, $rightName));
        $this->userService->updateUser($user);

        return new MeEntity($user->getArrayCopy());
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
        $userName = StaticNameService::generateRandomName();
        return new UserNameEntity($userName->userName);
    }

}
