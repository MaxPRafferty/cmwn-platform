<?php

namespace Notice\Factory;

use Notice\EmailModel\ForgotEmailModel;
use Notice\Listeners\ForgotPasswordListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ForgotPasswordListenerFactory
 * @package Notice\Factory
 */
class ForgotPasswordListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config       = $serviceLocator->get('Config');
        $forgotModel = new ForgotEmailModel(['image_domain' => $config['options']['image_domain']]);
        return new ForgotPasswordListener($forgotModel);
    }
}
