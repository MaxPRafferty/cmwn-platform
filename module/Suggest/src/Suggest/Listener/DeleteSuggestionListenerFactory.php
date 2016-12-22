<?php

namespace Suggest\Listener;

use Interop\Container\ContainerInterface;
use Suggest\Service\SuggestedServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class DeleteSuggestionListenerFactory
 */
class DeleteSuggestionListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new DeleteSuggestionListener($container->get(SuggestedServiceInterface::class));
    }
}
