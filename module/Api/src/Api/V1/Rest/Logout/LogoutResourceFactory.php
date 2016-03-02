<?php
namespace Api\V1\Rest\Logout;

class LogoutResourceFactory
{
    public function __invoke($services)
    {
        return new LogoutResource();
    }
}
