<?php

namespace Rule\Rule\Service;

use Rule\Rule\RuleInterface;
use Rule\Utils\AbstractConfigBuilderFactory;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Build a rule from a config string
 *
 * Functions much like the ZF3 AbstractConfigFactory.  This will check the config for a corresponding config key
 *
 * @see AbstractConfigFactory
 */
class BuildRuleFromConfigFactory extends AbstractConfigBuilderFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    protected $itemClassKey = 'action_class';

    /**
     * @inheritDoc
     */
    protected $instanceOf = RuleInterface::class;
}
