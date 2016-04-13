<?php

namespace Api\Listeners;

use Application\Exception\NotFoundException;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\Assertions\UserAssertion;
use Security\Exception\ChangePasswordException;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;

/**
 * Class UserRouteListener
 * @package Api\Listeners
 */
class UserRouteListener implements AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * UserRouteListener constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners['Zend\Mvc\Application'] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_ROUTE,
            [$this, 'onRoute']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $eventId => $listener) {
            $manager->detach($eventId, $listener);
        }
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

        try {
            $identity = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $identity = $changePassword->getUser();
        }

        $assertion = new UserAssertion($identity);
        $assertion->setUser($user);
        $event->setParam('assertion', $assertion);
    }
}
