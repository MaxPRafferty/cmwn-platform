<?php

namespace Sa\Rest\SuperAdminSettings;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuperAdminResourceFactory
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsResourceFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SuperAdminSettingsResource();
    }
}
