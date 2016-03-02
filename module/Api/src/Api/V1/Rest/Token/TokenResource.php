<?php
namespace Api\V1\Rest\Token;

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
        return new TokenEntity(['token' => 'bar']);
    }
}
