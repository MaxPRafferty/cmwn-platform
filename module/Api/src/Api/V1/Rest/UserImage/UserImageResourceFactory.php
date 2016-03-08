<?php
namespace Api\V1\Rest\UserImage;

class UserImageResourceFactory
{
    public function __invoke($services)
    {
        return new UserImageResource();
    }
}
