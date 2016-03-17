<?php

namespace Notice\Factory;

use AcMailer\Service\MailServiceAwareInterface;
use AcMailer\Service\MailServiceInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MailServiceAwareInitializer
 *
 * ${CARET}
 */
class MailServiceAwareInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $services
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $services)
    {
        if (!$instance instanceof MailServiceAwareInterface) {
            return;
        }

        /** @var MailServiceInterface $mailService */
        $mailService = $services->get('acmailer.mailservice.default');
        $instance->setMailService($mailService);
    }
}
