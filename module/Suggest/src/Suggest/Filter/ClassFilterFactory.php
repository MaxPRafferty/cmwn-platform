<?php

namespace Suggest\Filter;

use Group\Service\UserGroupService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ClassFilterFactory
 */
class ClassFilterFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ClassFilter($container->get(UserGroupService::class));
    }
}
