<?php

namespace Api\V1\Rest\Import;

use Interop\Container\ContainerInterface;
use Job\Service\JobServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ImportResourceFactory
 * @deprecated
 */
class ImportResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ImportResource($container->get(JobServiceInterface::class), $container);
    }
}
