<?php

namespace Rule\Engine\Service;

use Rule\Engine\Specification\SpecificationCollectionInterface;
use Rule\Engine\Specification\SpecificationInterface;
use Rule\Utils\AbstractCollectionBuilder;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class BuildSpecificationCollectionFactory
 */
class BuildSpecificationCollectionFactory extends AbstractCollectionBuilder implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    protected $collectionClassKey = 'specification_collection_class';

    /**
     * @inheritDoc
     */
    protected $collectionItemsKey = 'specifications';

    /**
     * @inheritDoc
     */
    protected $collectionInstanceOf = SpecificationCollectionInterface::class;

    /**
     * @inheritDoc
     */
    protected $itemInstanceOf = SpecificationInterface::class;

    /**
     * @inheritDoc
     */
    protected $pluginManagerName = SpecificationManager::class;
}
