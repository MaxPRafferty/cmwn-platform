<?php

namespace Rule\Action\Service;

use Rule\Action\ActionInterface;
use Rule\Action\Collection\ActionCollection;
use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Utils\AbstractCollectionBuilder;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that will build a collection of actions
 *
 * Config options:
 *
 * // Define an optional provider collection (optional will use $requestedName otherwise)
 * 'action_collection_class' => 'Some\Action\Collection',
 *
 * // Defines the providers to add to the collection
 * 'actions' => [
 *      // This will append the action
 *      new NoopAction(),
 *
 *      // This will GET the action from the manager
 *      'Manchuck\Action',
 *      Manchuck\Action::class,
 *
 *     // This will build a rule with options
 *     [
 *          // Name of the action to build
 *          'name'    => 'Some/Action/To/Build',
 *
 *          // Options passed into the builder
 *          'options' => ['foo', 'bar'],
 *     ]
 * ]
 *
 * @see ActionCollection
 */
class BuildActionCollectionFactory extends AbstractCollectionBuilder implements FactoryInterface
{
    /**
     * @var string
     */
    protected $collectionClassKey = 'action_collection_class';

    /**
     * @var string
     */
    protected $collectionItemsKey = 'actions';

    /**
     * @var string
     */
    protected $collectionInstanceOf = ActionCollectionInterface::class;

    /**
     * @var string
     */
    protected $itemInstanceOf = ActionInterface::class;

    /**
     * @var string
     */
    protected $pluginManagerName = ActionManager::class;
}
