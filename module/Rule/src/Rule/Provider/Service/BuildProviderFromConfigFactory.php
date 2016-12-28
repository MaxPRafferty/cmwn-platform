<?php

namespace Rule\Provider\Service;

use Rule\Provider\ProviderInterface;
use Rule\Utils\AbstractConfigBuilderFactory;
use Zend\Config\AbstractConfigFactory;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Build an provider from a config string
 *
 * Functions much like the ZF3 AbstractConfigFactory.  This will check the config for a corresponding config key
 *
 * @see AbstractConfigFactory
 */
class BuildProviderFromConfigFactory extends AbstractConfigBuilderFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    protected $itemClassKey = 'provider_class';

    /**
     * @inheritDoc
     */
    protected $instanceOf = ProviderInterface::class;

}
