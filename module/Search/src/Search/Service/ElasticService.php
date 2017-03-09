<?php

namespace Search\Service;

use Application\Exception\NotFoundException;
use Elasticsearch\Client;
use Search\SearchableDocumentInterface;
use Search\ElasticHydrator;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayObject;

/**
 * Service for talking to elastic search
 */
class ElasticService implements ElasticServiceInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var ElasticHydrator
     */
    protected $hydrator;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * ElasticService constructor.
     *
     * @param Client $client
     * @param ElasticHydrator $hydrator
     * @param array $config
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        Client $client,
        ElasticHydrator $hydrator,
        array $config,
        EventManagerInterface $eventManager
    ) {
        $this->client    = $client;
        $this->indexName = $config['index'];
        $this->hydrator  = $hydrator;
        $this->events    = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function searchByType(string $type, string $query, $prototype = null): ElasticAdapter
    {
        return $this->doSearch(
            [
                'index' => $this->indexName,
                'type'  => $type,
                'body'  => ['q' => $query],
            ],
            $prototype
        );
    }

    /**
     * @inheritdoc
     */
    public function search(string $query, $prototype = null): ElasticAdapter
    {
        return $this->doSearch(
            [
                'index' => $this->indexName,
                'body'  => ['q' => $query],
            ],
            $prototype
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchDocumentByTypeAndId(string $type, string $docId, $prototype = null)
    {
        $results = $this->client->get([
            'index' => $this->indexName,
            'type'  => $type,
            'id'    => $docId,
        ]);

        if (!$results['found']) {
            throw new NotFoundException(sprintf(
                'Could not find a document of type %s by id %s',
                $type,
                $docId
            ));
        }

        return $this->hydrator->hydrate($results, $prototype);
    }

    /**
     * @inheritdoc
     */
    public function saveDocument(SearchableDocumentInterface $document): bool
    {
        $data    = $this->hydrator->extract($document);
        $results = $this->client->index([
            'index' => $this->indexName,
            'id'    => $document->getDocumentId(),
            'type'  => $document->getDocumentType(),
            'body'  => $data,
        ]);

        return $results['created'] ?? false;
    }

    /**
     * @inheritdoc
     */
    public function deleteDocument(SearchableDocumentInterface $document): bool
    {
        $results = $this->client->delete([
            'index' => $this->indexName,
            'id'    => $document->getDocumentId(),
            'type'  => $document->getDocumentType(),
        ]);

        return $results['found'] ?? false;
    }

    /**
     * Performs the search and returns an Elastic Adapter
     *
     * @param array $params
     * @param null $prototype
     *
     * @return ElasticAdapter
     */
    protected function doSearch(array $params, $prototype = null): ElasticAdapter
    {
        $prototype = $prototype ?? new ArrayObject();

        return new ElasticAdapter(
            $params,
            $this->client,
            $this->hydrator,
            $prototype
        );
    }
}
