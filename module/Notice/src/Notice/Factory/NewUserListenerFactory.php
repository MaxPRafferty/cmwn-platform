<?php

namespace Notice\Factory;

use Interop\Container\ContainerInterface;
use Notice\EmailModel\NewUserModel;
use Notice\Listeners\NewUserEmailListener;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class NewUserListenerFactory
 */
class NewUserListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return new NewUserEmailListener(new NewUserModel(['image_domain' => $config['options']['image_domain']]));
    }
}
