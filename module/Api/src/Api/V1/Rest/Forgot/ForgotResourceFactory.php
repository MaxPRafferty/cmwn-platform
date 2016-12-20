<?php
namespace Api\V1\Rest\Forgot;

use Forgot\Service\ForgotServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ForgotResourceFactory
 */
class ForgotResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ForgotResource($container->get(ForgotServiceInterface::class));
    }
}
