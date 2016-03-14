<?php

namespace Api\V1\Rest\Import;

use Zend\Hydrator\HydrationInterface;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class ImportEntity
 */
class ImportEntity implements ArraySerializableInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * ImportEntity constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return ['token' => $this->token];
    }

    /**
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        // noop here to statisfy hydration interface
    }
}
