<?php
namespace Api\V1\Rest\Token;

class TokenResourceFactory
{
    public function __invoke($services)
    {
        return new TokenResource();
    }
}
