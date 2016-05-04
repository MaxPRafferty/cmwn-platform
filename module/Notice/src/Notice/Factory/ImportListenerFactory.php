<?php

namespace Notice\Factory;

use Notice\EmailModel\ImportSuccessModel;
use Notice\Listeners\ImportListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImportListenerFactory
 */
class ImportListenerFactory implements FactoryInterface
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
        $successModel = new ImportSuccessModel(['image_domain' => $config['options']['image_domain']]);
        return new ImportListener($successModel);
    }
}
