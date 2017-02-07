<?php

namespace Rule\Action\Service;

use Rule\Action\ActionInterface;
use Rule\Action\Collection\ActionCollectionInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * This is a ServiceContainer that will only allow actions to be built
 *
 * @method ActionInterface|ActionCollectionInterface build(string $name, array $options = [])
 */
class ActionManager extends AbstractPluginManager implements PluginManagerInterface
{
    /**
     * @var string
     */
    protected $instanceOf = ActionInterface::class;
}
