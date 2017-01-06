<?php

namespace Rule\Rule\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Creates the rule manager
 */
class RuleManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config     = $container->get('Config');
        $ruleConfig = $config['rules'] ?? [];

        return new RuleManager($container, $ruleConfig);
    }
}
