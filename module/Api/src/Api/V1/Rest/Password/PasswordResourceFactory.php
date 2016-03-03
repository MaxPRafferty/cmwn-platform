<?php
namespace Api\V1\Rest\Password;

class PasswordResourceFactory
{
    public function __invoke($services)
    {
        return new PasswordResource();
    }
}
