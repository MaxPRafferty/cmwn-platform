<?php

namespace Rule\Engine\Service;

use Interop\Container\ContainerInterface;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\Specification\EngineSpecification;
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

    /**
     * @param ContainerInterface $container
     * @param array $spec
     * @param SpecificationCollectionInterface $collection
     */
    protected function appendArraySpec(ContainerInterface $container, array $spec, $collection)
    {
        $this->append(
            $this->getPluginManager($container)->build(
                $spec['id'] ?? ArraySpecification::class,
                $spec
            ),
            $collection
        );
    }
}
