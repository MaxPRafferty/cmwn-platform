<?php

namespace Sa;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use ZF\Apigility\Provider\ApigilityProviderInterface;

/**
 * Class Module
 * @package Sa
 */
class Module implements ApigilityProviderInterface, ConfigProviderInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
