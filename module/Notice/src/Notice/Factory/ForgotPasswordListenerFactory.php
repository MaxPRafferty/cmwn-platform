<?php

namespace Notice\Factory;

use Interop\Container\ContainerInterface;
use Notice\EmailModel\ForgotEmailModel;
use Notice\Listeners\ForgotPasswordListener;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ForgotPasswordListenerFactory
 */
class ForgotPasswordListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config      = $container->get('config');
        $forgotModel = new ForgotEmailModel(['image_domain' => $config['options']['image_domain']]);

        return new ForgotPasswordListener($forgotModel);
    }
}
