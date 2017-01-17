<?php

namespace Notice\Factory;

use Interop\Container\ContainerInterface;
use Notice\EmailModel\ImportSuccessModel;
use Notice\Listeners\ImportListener;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ImportListenerFactory
 */
class ImportListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return new ImportListener(new ImportSuccessModel(['image_domain' => $config['options']['image_domain']]));
    }
}
