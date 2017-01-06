<?php

namespace User\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class UserDelegatorFactory
 */
class UserDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $events = $container->get(EventManagerInterface::class);

        $hideListener = new HideDeletedEntitiesListener(['fetch.all.users'], ['fetch.user.post']);
        $hideListener->setEntityParamKey('user');
        $hideListener->setDeletedField('u.deleted');
        $hideListener->attach($events);

        $checkUser = new CheckUserListener();
        $checkUser->attach($events);

        return new UserServiceDelegator(
            $callback(),
            $events
        );
    }
}
