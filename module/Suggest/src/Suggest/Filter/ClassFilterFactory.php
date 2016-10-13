<?php

namespace Suggest\Filter;

use Group\Service\UserGroupService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClassFilterFactory
 * @package Suggest\Filter
 */
class ClassFilterFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $userGroupService = $serviceLocator->get(UserGroupService::class);
        return new ClassFilter($userGroupService);
    }
}
