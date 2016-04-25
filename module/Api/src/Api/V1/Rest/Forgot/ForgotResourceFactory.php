<?php
namespace Api\V1\Rest\Forgot;

use Forgot\Service\ForgotServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ForgotResourceFactory
 */
class ForgotResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Forgot\Service\ForgotServiceInterface $forgotService */
        $forgotService = $serviceLocator->get(ForgotServiceInterface::class);
        return new ForgotResource($forgotService);
    }
}
