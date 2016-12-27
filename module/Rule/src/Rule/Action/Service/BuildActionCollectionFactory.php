<?php

namespace Rule\Action\Service;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionInterface;
use Rule\Action\Collection\ActionCollectionInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that will build a collection of actions
 *
 * This is intended to be used with the ActionManager::build but can just be injected to create an empty collection
 */
class BuildActionCollectionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = is_array($options) ? $options : [];

        // allow empty collections to be build
        if (empty($options)) {
            return new $requestedName();
        }

        /** @var ActionManager $actionManager */
        $collectionClass  = $options['action_collection_class'] ?? $requestedName;
        $actionCollection = new $collectionClass();

        if (!$actionCollection instanceof ActionCollectionInterface) {
            throw new ServiceNotCreatedException(
                sprintf('%s is not a valid action collection', $collectionClass)
            );
        }

        unset($options['action_collection_class']);
        $actionManager     = $container->get(ActionManager::class);
        $collectionActions = $options['actions'] ?? $options;

        array_walk($collectionActions, function ($collectionSpec) use (&$actionCollection, &$actionManager) {
            $actionSpec = is_array($collectionSpec) && isset($collectionSpec['action'])
                ? $collectionSpec['action']
                : $collectionSpec;

            switch (true) {
                // Get the action from the manager
                case is_string($actionSpec):
                    $actionSpec = $actionManager->get($actionSpec);
                // we want to fall through

                // Append a built action
                case ($actionSpec instanceof ActionInterface):
                    $actionCollection->append($actionSpec);
                    break;

                // Build the action
                case is_array($actionSpec):
                    $actionName    = $actionSpec['name'] ?? null;
                    $actionOptions = $actionSpec['options'] ?? [];
                    $actionCollection->append(
                        $actionManager->build($actionName, $actionOptions)
                    );
                    break;

                default:
                    throw new ServiceNotCreatedException('Invalid Action spec type');
            }
        });

        return $actionCollection;
    }
}
