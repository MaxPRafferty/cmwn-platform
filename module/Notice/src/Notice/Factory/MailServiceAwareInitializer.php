<?php

namespace Notice\Factory;

use AcMailer\Service\MailServiceAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class MailServiceAwareInitializer
 */
class MailServiceAwareInitializer implements InitializerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof MailServiceAwareInterface) {
            return;
        }

        $instance->setMailService($container->get('acmailer.mailservice.default'));
    }
}
