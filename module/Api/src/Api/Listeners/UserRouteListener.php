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
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\ContentNegotiation\ParameterDataContainer;

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
     * @var UserAssertion
     */
    protected $assertion;

    /**
     * UserRouteListener constructor.
     *
     * @param UserServiceInterface $userService
     * @param UserAssertion $assertion
     */
    public function __construct(UserServiceInterface $userService, UserAssertion $assertion)
    {
        $this->userService = $userService;
        $this->assertion   = $assertion;
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

        $this->listeners['Zend\Mvc\Application'] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_ROUTE,
            [$this, 'injectCurrentValues'],
            -649
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
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            return null;
        }

        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return null;
        }

        $route  = $event->getRouteMatch();
        $userId = $route->getParam('user_id', false);

        if ($userId === false) {
            return null;
        }

        try {
            $user = $this->userService->fetchUser($userId);
        } catch (NotFoundException $notFound) {
            return new ApiProblemResponse(new ApiProblem(404, 'User not found'));
        }

        $route->setParam('user', $user);
        $this->addAssertion($event, $user);
        return null;
    }

    /**
     * @param MvcEvent      $event
     * @param UserInterface $user
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

        $assertion = clone $this->assertion;
        $assertion->setActiveUser($identity);
        $assertion->setRequestedUser($user);
        $event->setParam('assertion', $assertion);
    }

    /**
     * @param MvcEvent $event
     * @return null|void
     */
    public function injectCurrentValues(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            return ;
        }

        if ($request->getMethod() !== Request::METHOD_PUT) {
            return;
        }

        $dataContainer = $event->getParam('ZFContentNegotiationParameterData', false);
        if (!$dataContainer instanceof ParameterDataContainer) {
            return null;
        }

        $user = $event->getRouteMatch()->getParam('user');
        if (!$user instanceof UserInterface) {
            return null;
        }

        if ($user->getType() === UserInterface::TYPE_CHILD) {
            $dataContainer->setBodyParam('email', $user->getEmail());
        }
    }
}
