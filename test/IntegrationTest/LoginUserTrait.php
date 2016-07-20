<?php

namespace IntegrationTest;

use Security\ChangePasswordUser;
use Security\Service\SecurityService;
use User\UserInterface;
use Zend\Authentication\AuthenticationService;

/**
 * Trait LoginUserTrait
 */
trait LoginUserTrait
{

    /**
     * Logs in a user (from the test DB)
     *
     * @param $userName
     * @return UserInterface
     */
    public function logInUser($userName)
    {
        /** @var SecurityService $userService */
        $userService = TestHelper::getServiceManager()->get(SecurityService::class);

        $user = $userService->fetchUserByUserName($userName);
        $this->getAuthService()->getStorage()->write($user);
        return $user;
    }

    /**
     * Logs in a user (from the test DB)
     *
     * @param $userName
     */
    public function logInChangePasswordUser($userName)
    {
        $user = new ChangePasswordUser($this->logInUser($userName)->getArrayCopy());
        $this->getAuthService()->getStorage()->write($user);
    }

    /**
     * @return AuthenticationService
     */
    protected function getAuthService()
    {
        return TestHelper::getServiceManager()->get(AuthenticationService::class);
    }

    /**
     * @after
     */
    public function logOutUser()
    {
        $this->getAuthService()->clearIdentity();
    }
}
