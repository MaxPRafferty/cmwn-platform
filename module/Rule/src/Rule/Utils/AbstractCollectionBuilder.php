<?php

namespace Rule\Utils;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionInterface;
use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Action\Service\BuildActionCollectionFactory;
use Rule\Engine\Specification\SpecificationCollectionInterface;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\ProviderInterface;
use Rule\Provider\Service\BuildProviderCollectionFactory;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\Service\BuildRuleCollectionFactory;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * An abstract class that will reduce the dryness of all the collection builders
 *
 * @see BuildRuleCollectionFactory
 * @see BuildProviderCollectionFactory
 * @see BuildActionCollectionFactory
 */
abstract class AbstractCollectionBuilder implements FactoryInterface
{
    /**
     * @var string name of the key in the options that specifies the collection class
     */
    protected $collectionClassKey;

    /**
     * @var string name of the key in options that specifies the items
     */
    protected $collectionItemsKey;

    /**
     * @var string name of the class the collection must be an instance of
     */
    protected $collectionInstanceOf;

    /**
     * @var string name of the class each item must be an instance of
     */
    protected $itemInstanceOf;

    /**
     * @var string name of the plugin manager to get
     */
    protected $pluginManagerName;

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options         = is_array($options) ? $options : [];
        $collectionClass = $options[$this->collectionClassKey] ?? $requestedName;
        $collection      = new $collectionClass();

        if (!$collection instanceof $this->collectionInstanceOf) {
            throw new ServiceNotCreatedException(
                sprintf(
                    '"%s" is not a valid collection that "%s" can build',
                    $collectionClass,
                    static::class
                )
            );
        }

        unset($options[$this->collectionClassKey]);
        $collectionSpecs = $options[$this->collectionItemsKey] ?? $options;

        array_walk($collectionSpecs, function ($itemSpec) use (&$container, &$collection) {

            switch (true) {
                // Get the item from the plugin manager
                case is_string($itemSpec):
                    $this->appendStringSpec($container, $itemSpec, $collection);
                    break;

                // Append the passed in item
                case ($itemSpec instanceof $this->itemInstanceOf):
                    $this->append($itemSpec, $collection);
                    break;

                // Build the item
                case is_array($itemSpec):
                    $this->appendArraySpec($container, $itemSpec, $collection);
                    break;

                default:
                    throw new ServiceNotCreatedException('Invalid Item specification type');
            }
        });

        return $collection;
    }

    /**
     * @param ContainerInterface $container
     * @param string $key
     * @param RuleCollectionInterface|ProviderCollectionInterface|ActionCollectionInterface $collection
     */
    protected function appendStringSpec(ContainerInterface $container, string $key, $collection)
    {
        if (!$this->getPluginManager($container)->has($key)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    '"%s" was not found in the container for "%s"',
                    $key,
                    static::class
                )
            );
        }

        $this->append(
            $this->getPluginManager($container)->get($key),
            $collection
        );
    }

    /**
     * @param ContainerInterface $container
     * @param array $spec
     * @param RuleCollectionInterface|ProviderCollectionInterface|ActionCollectionInterface $collection
     */
    protected function appendArraySpec(ContainerInterface $container, array $spec, $collection)
    {
        $this->append(
            $this->getPluginManager($container)->build(
                $spec['name'] ?? null,
                $spec['options'] ?? []
            ),
            $collection
        );
    }

    /**
     * @param RuleInterface|ProviderInterface|ActionInterface $instance
     * @param RuleCollectionInterface|ProviderCollectionInterface|ActionCollectionInterface|SpecificationCollectionInterface $collection
     */
    protected function append($instance, $collection)
    {
        $this->validateItem($instance);
        $collection->append($instance);
    }

    /**
     * @param object $instance
     *
     * @return bool
     */
    protected function validateItem($instance): bool
    {
        if (!$instance instanceof $this->itemInstanceOf) {
            throw new ServiceNotCreatedException(
                sprintf(
                    '"%s" is not a valid item that "%s" can add to the collection',
                    get_class($instance),
                    static::class
                )
            );
        }

        return true;
    }

    /**
     * Gets the plugin manager
     *
     * @param ContainerInterface $container
     *
     * @return PluginManagerInterface
     */
    protected function getPluginManager(ContainerInterface $container): PluginManagerInterface
    {
        if (!$container->has($this->pluginManagerName)) {
            throw new ServiceNotCreatedException(
                sprintf('Cannot find the plugin manager "%s" in the container', $this->pluginManagerName)
            );
        }

        return $container->get($this->pluginManagerName);
    }
}
