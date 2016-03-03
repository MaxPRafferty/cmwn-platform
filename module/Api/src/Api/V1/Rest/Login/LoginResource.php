<?php
namespace Api\V1\Rest\Login;

use Api\V1\Rest\User\MeEntity;
use Security\Authentication\AuthenticationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class LoginResource
 *
 * @package Api\V1\Rest\Login
 */
class LoginResource extends AbstractResourceListener
{
    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        if ($this->authService->hasIdentity()) {
            return new MeEntity($this->authService->getIdentity());
        }

        $this->authService->getAdapter()->setUserIdentifier(
            $this->getInputFilter()->getValue('username')
        );

        $this->authService->getAdapter()->setPassword(
            $this->getInputFilter()->getValue('password')
        );

        $result = $this->authService->authenticate();

        if (!$result->isValid()) {
            return new ApiProblem(401, "Invalid Login");
        }

        return new MeEntity($result->getIdentity());
    }
}
