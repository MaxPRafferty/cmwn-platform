<?php

namespace Api;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use ZF\Apigility\Provider\ApigilityProviderInterface;

/**
 * Class Module
 * @package Api
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
