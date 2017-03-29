<?php

namespace Search\Service;

use Elasticsearch\Client;
use Search\ElasticHydrator;
use Search\Exception\RuntimeException;
use Zend\Hydrator\Iterator\HydratingArrayIterator;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Stdlib\ArrayObject;

/**
 * A paginator adapter for elastic search
 */
class ElasticAdapter implements AdapterInterface
{
    /**
     * @var ArrayObject
     */
    protected $params;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var null
     */
    protected $prototype;

    /**
     * @var ElasticHydrator
     */
    protected $hydrator;

    /**
     * @var array
     */
    protected $results;

    /**
     * ElasticAdapter constructor.
     *
     * @param array|ArrayObject $params
     * @param Client $client
     * @param ElasticHydrator $hydrator
     * @param null $prototype
     */
    public function __construct(
        $params,
        Client $client,
        ElasticHydrator $hydrator,
        $prototype = null
    ) {
        if (!is_array($params) || $params instanceof ArrayObject) {
            throw new RuntimeException(
                'Params needs to be an array or ArrayObject for ' . static::class
            );
        }

        $this->params    = $params instanceof ArrayObject ? $params : new ArrayObject($params);
        $this->client    = $client;
        $this->prototype = $prototype ?? new ArrayObject();
        $this->hydrator  = $hydrator;
    }

    /**
     * @inheritDoc
     */
    public function getItems($offset, $itemCountPerPage)
    {
        if ($offset > 0) {
            $this->params['from'] = $offset;
        }

        $this->params['size'] = $itemCountPerPage;

        return new HydratingArrayIterator(
            $this->hydrator,
            $this->getResults()['hits']['hits'] ?? [],
            $this->prototype
        );
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return (int)$this->getResults()['hits']['total'] ?? 0;
    }

    /**
     * Performs the search to elastic and stores them locally
     *
     * @return array
     */
    protected function getResults(): array
    {
        if ($this->results == null) {
            $this->results = $this->client->search($this->params->getArrayCopy());
        }

        return $this->results;
    }
}
