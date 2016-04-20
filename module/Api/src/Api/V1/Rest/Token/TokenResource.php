<?php
namespace Api\V1\Rest\Token;

use Api\V1\Rest\User\MeEntity;
use Security\Exception\ChangePasswordException;
use Zend\Authentication\AuthenticationServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class TokenResource
 * @package Api\V1\Rest\Token
 */

class TokenResource extends AbstractResourceListener
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
        if (!$this->authService->hasIdentity()) {
            return new TokenEntity([]);
        }

        try {
            $identity = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $identity = $changePassword->getUser();
        }

        return new MeEntity($identity);
    }
}
