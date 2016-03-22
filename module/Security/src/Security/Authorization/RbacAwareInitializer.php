<?php

namespace Security\Authorization;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RbacAwareInitializer
 *
 * ${CARET}
 */
class RbacAwareInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceof RbacAwareInterface) {
            return;
        }

        /** @var \Security\Authorization\Rbac $rbac */
        $rbac = $serviceLocator->get('Security\Authorization\Rbac');
        $instance->setRbac($rbac);
    }
}
