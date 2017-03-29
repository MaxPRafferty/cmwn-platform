<?php

namespace Search\Rule\Action;

use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use Search\SearchableDocumentInterface;
use Search\Service\ElasticServiceInterface;

/**
 * Saves a document in search
 */
class SaveDocumentAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var ElasticServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $documentProvider;

    /**
     * SaveDocumentAction constructor.
     *
     * @param ElasticServiceInterface $service
     * @param string $documentProvider
     */
    public function __construct(ElasticServiceInterface $service, string $documentProvider)
    {
        $this->service          = $service;
        $this->documentProvider = $documentProvider;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        $document = $item->getParam($this->documentProvider);
        static::checkValueType($document, SearchableDocumentInterface::class);

        $this->service->saveDocument($document);
    }
}
