<?php

namespace Suggest\Rule;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class RuleCollectionFactory
 *
 * @todo port to rules enginex
 */
class RuleCollectionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config           = $container->get('config');
        $suggestionConfig = isset($config['suggestion-engine']) ? $config['suggestion-engine'] : [];
        $rulesConfig      = isset($suggestionConfig['rules']) ? $suggestionConfig['rules'] : [];

        return new RuleCollection($container, $rulesConfig);
    }
}
