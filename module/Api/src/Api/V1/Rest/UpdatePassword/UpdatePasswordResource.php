<?php

namespace Api\V1\Rest\UpdatePassword;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Security\Service\SecurityServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class UpdatePasswordResource
 */
class UpdatePasswordResource extends AbstractResourceListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * PasswordResource constructor.
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(SecurityServiceInterface $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $data = (array) $data;
        if (!$this->authService->hasIdentity()) {
            return new ApiProblem(401, 'Not Authorized');
        }

        try {
            $securityUser = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $securityUser = $changePassword->getUser();
        }

        if (!$securityUser instanceof SecurityUser) {
            return new ApiProblem(401, 'Not Authorized');
        }

        $this->securityService->savePasswordToUser($securityUser, $data['password']);
        $this->authService->clearIdentity();
        return new ApiProblem(200, 'Password Updated', null, 'Ok');
    }
}
