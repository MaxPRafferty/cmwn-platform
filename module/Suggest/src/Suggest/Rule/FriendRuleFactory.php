<?php

namespace Suggest\Rule;

use Friend\Service\FriendService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FriendRuleFactory
 */
class FriendRuleFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FriendRule($container->get(FriendService::class));
    }
}
