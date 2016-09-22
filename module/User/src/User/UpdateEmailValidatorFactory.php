<?php
/**
 * Created by PhpStorm.
 * User: chaitu
 * Date: 8/10/16
 * Time: 10:23 AM
 */

namespace User;

use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class UpdateEmailValidatorFactory
 * @package User
 */
class UpdateEmailValidatorFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return UpdateEmailValidator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        /**@var UserServiceInterface $userService*/
        $userService = $serviceLocator->get(UserServiceInterface::class);
        return new UpdateEmailValidator([], $userService);
    }
}
