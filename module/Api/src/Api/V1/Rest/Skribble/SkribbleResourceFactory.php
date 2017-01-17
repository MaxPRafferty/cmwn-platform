<?php

namespace Api\V1\Rest\Skribble;

use Interop\Container\ContainerInterface;
use Skribble\Service\SkribbleServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SkribbleResourceFactory
 */
class SkribbleResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SkribbleResource(
            $container->get(SkribbleServiceInterface::class),
            $container->get('SkribbleSns')
        );
    }
}
