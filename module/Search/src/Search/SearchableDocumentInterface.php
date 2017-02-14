<?php

namespace Search;

/**
 * An Interface that allows an object to be added to elastic search
 *
 * @SWG\Definition(
 *     definition="Searchable",
 *     description="This entity is searchable"
 * )
 */
interface SearchableDocumentInterface
{
    /**
     * Defines the type of document
     *
     * This transforms into part of the path for a elastic search
     *
     * @return string
     */
    public function getDocumentType(): string;

    /**
     * Defines the means to get the identifier of the document
     *
     * @return string
     */
    public function getDocumentId(): string;
}
