<?php
namespace Api\V1\Rest\Token;

use Api\V1\Rest\User\MeEntity;
use User\Adult;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class TokenResource extends AbstractResourceListener
{
    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $user = new Adult();

        $user->setUserId('foo-bar-baz-bat');
        return new MeEntity($user);

//        return new TokenEntity(['token' => $csrf->getHash()]);
    }
}
