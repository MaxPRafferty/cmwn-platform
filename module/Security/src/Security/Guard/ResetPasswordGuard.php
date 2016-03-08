<?php

namespace Security\Guard;

use Zend\Authentication\AuthenticationServiceInterface;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

/**
 * Class ResetPasswordGuard
 * @package Security\Guard
 */
class ResetPasswordGuard implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * ResetPasswordGuard constructor.
     *
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch']);
    }

    public function onDispatch(MvcEvent $event)
    {
        // Coming in from the console
        if (!$event->getRequest() instanceof HttpRequest) {
            return;
        }

        // If the user is not logged in, that is fine
        if (!$this->authService->hasIdentity()) {
            return;
        }

        $router = $event->getRouteMatch();
        if (strpos($router->getMatchedRouteName(), 'zf-apigility') !== false) {
            return;
        }

        $application    = $event->getApplication();
        $user = $this->authService->getIdentity();
        if (!$user instanceof ChangePasswordUser) {
            return ;
        }

        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $event->setError('Change Password');
        $event->setParam('exception', new ChangePasswordException($user));

        $application->getEventManager()->trigger($event);
        return $event->getResult();
    }
}
