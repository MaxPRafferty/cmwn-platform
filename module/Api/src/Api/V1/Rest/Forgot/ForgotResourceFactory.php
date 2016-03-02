<?php
namespace Api\V1\Rest\Forgot;

class ForgotResourceFactory
{
    public function __invoke($services)
    {
        return new ForgotResource();
    }
}
