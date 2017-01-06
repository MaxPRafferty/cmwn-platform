<?php

namespace Rule\Engine\Service;

use Rule\Engine\Specification\SpecificationCollection;
use Rule\Engine\Specification\SpecificationInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * A Service manager that only creates specifications for the rules engine
 */
class SpecificationManager extends AbstractPluginManager implements PluginManagerInterface
{
    /**
     * @param object $instance
     */
    public function validate($instance)
    {
        if (empty($this->instanceOf)
            || $instance instanceof SpecificationInterface
            || $instance instanceof SpecificationCollection
        ) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            __CLASS__,
            $this->instanceOf,
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }
}
