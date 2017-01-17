<?php
namespace Api\V1\Rest\Media;

use Interop\Container\ContainerInterface;
use Media\Service\MediaServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class MediaResourceFactory
 */
class MediaResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new MediaResource($container->get(MediaServiceInterface::class));
    }
}
