<?php

namespace Api\V1\Rest\Import;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class ImportEntity
 * @deprecated
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
        return ['job_id' => $this->token];
    }

    /**
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        // noop here to statisfy hydration interface
    }
}
