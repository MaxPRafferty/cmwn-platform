<?php

namespace Notice\Factory;

use Notice\EmailModel\NewUserModel;
use Notice\Listeners\NewUserEmailListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class NewUserListenerFactory
 * @package Notice\Factory
 */
class NewUserListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return NewUserEmailListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config       = $serviceLocator->get('Config');
        $successModel = new NewUserModel(['image_domain' => $config['options']['image_domain']]);

        return new NewUserEmailListener($successModel);
    }
}
