<?php

namespace Api\Listeners;

use Application\Exception\NotFoundException;
use Security\Authorization\Assertions\UserAssertion;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Authentication\AuthenticationServiceInterface;
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

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    public function __construct(UserServiceInterface $userService, AuthenticationServiceInterface $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
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
        $this->addAssertion($event, $user);
        return null;
    }

    /**
     * @param MvcEvent $event
     */
    protected function addAssertion(MvcEvent $event, UserInterface $user)
    {
        if (!$this->authService->hasIdentity()) {
            return;
        }

        $assertion = new UserAssertion($this->authService->getIdentity());
        $assertion->setUser($user);
        $event->setParam('assertion', $assertion);
    }
}
