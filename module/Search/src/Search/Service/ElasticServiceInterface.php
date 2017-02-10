<?php

namespace Search\Service;

use Application\Exception\NotFoundException;
use Search\SearchableDocumentInterface;

/**
 * An Interface describing how to talk to elastic search
 */
interface ElasticServiceInterface
{
    /**
     * Search by a document type
     *
     * @param string $type
     * @param string $query
     * @param null $prototype
     *
     * @return ElasticAdapter
     */
    public function searchByType(string $type, string $query, $prototype = null): ElasticAdapter;

    /**
     * Search by all types
     *
     * @param string $query
     * @param null $prototype
     *
     * @return ElasticAdapter
     */
    public function search(string $query, $prototype = null): ElasticAdapter;

    /**
     * Fetches on document from elastic search
     *
     * @param string $type
     * @param string $docId
     * @param null $prototype
     *
     * @return object
     * @throws NotFoundException
     */
    public function fetchDocumentByTypeAndId(string $type, string $docId, $prototype = null);

    /**
     * Saves a SearchableDocument to elastic
     *
     * @param SearchableDocumentInterface $document
     *
     * @return bool
     */
    public function saveDocument(SearchableDocumentInterface $document): bool;

    /**
     * @param SearchableDocumentInterface $document
     *
     * @return bool
     */
    public function deleteDocument(SearchableDocumentInterface $document): bool;
}
