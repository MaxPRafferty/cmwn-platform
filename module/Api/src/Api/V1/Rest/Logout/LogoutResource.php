<?php
namespace Api\V1\Rest\Logout;

use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\ArrayObject;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class LogoutResource
 *
 * @package Api\V1\Rest\Logout
 */
class LogoutResource extends AbstractResourceListener
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $this->authService->clearIdentity();
        $this->authService->getStorage()->clear();
        return [];
    }
}
