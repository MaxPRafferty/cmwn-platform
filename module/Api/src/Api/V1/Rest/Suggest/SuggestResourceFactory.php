<?php

namespace Api\V1\Rest\Suggest;

use Interop\Container\ContainerInterface;
use Suggest\Service\SuggestedServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SuggestResourceFactory
 */
class SuggestResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SuggestResource($container->get(SuggestedServiceInterface::class));
    }
}
