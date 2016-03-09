<?php
namespace Api\V1\Rest\Login;

use Api\V1\Rest\User\MeEntity;
use Security\Authentication\AuthAdapter;
use Zend\Authentication\AuthenticationService;
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
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var AuthAdapter
     */
    protected $adapter;

    public function __construct(AuthenticationService $authService, AuthAdapter $adapter)
    {
        $this->authService = $authService;
        $this->adapter     = $adapter;
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
            $this->authService->clearIdentity();
            $this->authService->getStorage()->clear();
        }

        $this->adapter->setUserIdentifier(
            $this->getInputFilter()->getValue('username')
        );

        $this->adapter->setPassword(
            $this->getInputFilter()->getValue('password')
        );

        $result = $this->authService->authenticate($this->adapter);

        if (!$result->isValid()) {
            return new ApiProblem(401, "Invalid Login");
        }

        return new MeEntity($result->getIdentity());
    }
}
