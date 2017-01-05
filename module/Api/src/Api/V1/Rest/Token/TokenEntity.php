<?php

namespace Api\V1\Rest\Token;

use Api\TokenEntityInterface;
use ZF\Hal\Entity;

/**
 * Class TokenEntity
 */
class TokenEntity extends Entity implements TokenEntityInterface
{
    /**
     * TokenEntity constructor.
     * @param array|object $entity
     */
    public function __construct($entity)
    {
        parent::__construct(new \ArrayObject(), null);
    }

    /**
     * @return DefaultLinksCollection
     */
    public function getLinks()
    {
        return new DefaultLinksCollection();
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->getEntity()['token'] = $token;
    }
}
