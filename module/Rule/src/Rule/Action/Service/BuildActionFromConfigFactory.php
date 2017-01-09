<?php

namespace Rule\Action\Service;

use Rule\Action\ActionInterface;
use Rule\Utils\AbstractConfigBuilderFactory;
use Zend\Config\AbstractConfigFactory;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Build an action from a config string
 *
 * Functions much like the ZF3 AbstractConfigFactory.  This will check the config for a corresponding config key
 *
 * @see AbstractConfigFactory
 */
class BuildActionFromConfigFactory extends AbstractConfigBuilderFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    protected $itemClassKey = 'action_class';

    /**
     * @inheritDoc
     */
    protected $instanceOf = ActionInterface::class;
}
