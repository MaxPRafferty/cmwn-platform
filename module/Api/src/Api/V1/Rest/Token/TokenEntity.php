<?php
namespace Api\V1\Rest\Token;

use ZF\Hal\Entity;

class TokenEntity extends Entity
{
    public function getLinks()
    {
        return new DefaultLinksCollection();
    }
}
