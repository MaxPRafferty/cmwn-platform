<?php

namespace Rule\Action\Service;

use Interop\Container\ContainerInterface;
use Rule\Action\ActionInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that can be used to build an action
 *
 * Intended for use with the ActionManager::build(), however can be used much like the ZF3 invokable factory
 *
 * Every key in the $options will be check for a corresponding name in the container.  If not then the natural
 * value will be passed into the class on construction
 */
class BuildActionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options     = is_array($options) ? $options : [];
        $actionClass = $options['action_class'] ?? $requestedName;
        unset($options['action_class']);

        if (!in_array(ActionInterface::class, class_implements($actionClass))) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'Cannot create "%s" using "%s" since "%s" is not an action',
                    $actionClass,
                    self::class,
                    $actionClass
                )
            );
        }

        $arguments = array_map(
            function ($specValue) use (&$container) {
                // Check for a dependency from the SM
                if (is_string($specValue) && $container->has($specValue)) {
                    return $container->get($specValue);
                }

                return $specValue;
            },
            $options
        );

        return new $actionClass(...$arguments);
    }
}
