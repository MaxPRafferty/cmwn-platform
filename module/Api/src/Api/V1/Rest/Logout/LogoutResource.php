<?php
namespace Api\V1\Rest\Logout;

use Security\Authentication\AuthenticationServiceInterface;
use Zend\Stdlib\ArrayObject;
use ZF\ApiProblem\ApiProblem;
use ZF\Hal\Entity;
use ZF\Rest\AbstractResourceListener;

/**
 * Class LogoutResource
 *
 * @package Api\V1\Rest\Logout
 */
class LogoutResource extends AbstractResourceListener
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
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $this->authService->clearIdentity();
        return new LogoutEntity();
    }
}
