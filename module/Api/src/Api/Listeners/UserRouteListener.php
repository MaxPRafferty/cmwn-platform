<?php

namespace Api\Listeners;

use Application\Exception\NotFoundException;
use User\Service\UserServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;

/**
 * Class UserRouteListener
 * @package Api\Listeners
 */
class UserRouteListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
    }

    /**
     * @param MvcEvent $event
     * @return void|ApiProblem
     */
    public function onRoute(MvcEvent $event)
    {
        $route  = $event->getRouteMatch();
        $userId = $route->getParam('user_id', false);

        if ($userId === false) {
            return null;
        }

        try {
            $user = $this->userService->fetchUser($userId);
        } catch (NotFoundException $notFound) {
            return new ApiProblem(404, 'User not found');
        }

        $route->setParam('user', $user);
        return null;
    }
}
