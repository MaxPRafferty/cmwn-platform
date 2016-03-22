<?php

namespace Security\Guard;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Zend\Authentication\AuthenticationServiceInterface;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\MvcEvent;

/**
 * Class ResetPasswordGuard
 * @package Security\Guard
 */
class ResetPasswordGuard implements AuthenticationServiceAwareInterface
{

    use AuthenticationServiceAwareTrait;

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch']);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach('*', $listener);
        }
    }
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
     * @param MvcEvent $event
     * @return mixed|void
     */
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
