<?php

namespace Api\V1\Rest\Password;

use Security\Service\SecurityServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class PasswordResource
 */
class PasswordResource extends AbstractResourceListener
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * PasswordResource constructor.
     * @param AuthenticationServiceInterface $authService
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(AuthenticationServiceInterface $authService, SecurityServiceInterface $securityService)
    {
        $this->authService     = $authService;
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

        $securityUser = $this->authService->getIdentity();

        $this->securityService->savePasswordToUser($securityUser, $data['password']);
        $this->authService->clearIdentity();
        return new ApiProblem(200, 'Password Updated', null, 'Ok');
    }
}
