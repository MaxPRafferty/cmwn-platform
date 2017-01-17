<?php

namespace Rule\Rule\Service;

use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\RuleInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * This is a ServiceContainer that will only allow Rules or Rule Collections to be built
 *
 * @method RuleCollectionInterface|RuleInterface build(string $name, array $options = [])
 */
class RuleManager extends AbstractPluginManager implements PluginManagerInterface
{
    /**
     * @var string
     */
    protected $instanceOf = RuleInterface::class;
}
