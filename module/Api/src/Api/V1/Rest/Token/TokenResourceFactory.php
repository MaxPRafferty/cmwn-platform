<?php

namespace Api\V1\Rest\Token;

use Interop\Container\ContainerInterface;
use Security\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class TokenResourceFactory
 */
class TokenResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TokenResource($container->get(AuthenticationService::class));
    }
}
