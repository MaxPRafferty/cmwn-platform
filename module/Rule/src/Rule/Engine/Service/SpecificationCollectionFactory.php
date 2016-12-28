<?php

namespace Rule\Engine\Service;

use Interop\Container\ContainerInterface;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\Specification\SpecificationCollection;
use Rule\Engine\Specification\SpecificationCollectionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SpecificationCollectionFactory
 */
class SpecificationCollectionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Start with all the factories
        $config       = $container->get('Config');
        $allFactories = $config['specifications']['factories'];
        unset($allFactories[ArraySpecification::class]);
        unset($allFactories[SpecificationCollection::class]);
        unset($allFactories[$requestedName]);

        // Merge in configured specs
        $allSpecs = array_merge(
            array_keys($allFactories),
            array_keys($config[BuildSpecificationFromConfigFactory::class] ?? [])
        );

        return $container->get(SpecificationManager::class)
            ->build(
                SpecificationCollectionInterface::class,
                ['specifications' => $allSpecs]
            );
    }
}
