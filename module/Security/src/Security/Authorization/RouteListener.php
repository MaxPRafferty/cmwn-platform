<?php

namespace Security\Authorization;

use Application\Utils\NoopLoggerAwareTrait;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\Assertions\DefaultAssertion;

use Security\OpenRouteTrait;
use Security\SecurityUser;
use Security\Service\SecurityOrgService;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class RouteListener
 */
class RouteListener implements RbacAwareInterface, AuthenticationServiceAwareInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;
    use OpenRouteTrait;

    /**
     * @var array|mixed
     */
    protected $routePerms = [];

    /**
     * @var SecurityOrgService
     */
    protected $orgService;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * RouteListener constructor.
     *
     * @param array $config
     * @param SecurityOrgService $orgService
     */
    public function __construct(
        array $config,
        SecurityOrgService $orgService
    ) {
        $this->setOpenRoutes(isset($config['open-routes']) ? $config['open-routes'] : []);
        $this->routePerms  = isset($config['route-permissions']) ? $config['route-permissions'] : [];
        $this->orgService  = $orgService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], (PHP_INT_MAX - 2));
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
     * @param MvcEvent $event
     * @return void|ApiProblemResponse
     */
    public function onDispatch(MvcEvent $event)
    {
        if ($this->isRouteUnRestricted($event)) {
            return null;
        }

        if (!$this->authService->hasIdentity()) {
            $routeName = $event->getRouteMatch()->getMatchedRouteName();
            $this->getLogger()->alert(
                sprintf('An attempt was made to access restricted route [%s] when not logged in', $routeName)
            );

            return new ApiProblemResponse(new ApiProblem(401, 'Authentication failed'));
        }

        /** @var SecurityUser $user */
        $user = $this->authService->getIdentity();

        if ($user->isSuper()) {
            $user->setRole('super');
            return null;
        }

        $user->setRole($this->getRoleForGroup($event));
        return $this->isRouteAllowed($event, $user);
    }

    /**
     *
     * @param MvcEvent $event
     * @param SecurityUser $user
     * @return null|ApiProblemResponse
     */
    protected function isRouteAllowed(MvcEvent $event, SecurityUser $user)
    {
        $routeName = $event->getRouteMatch()->getMatchedRouteName();
        if (!array_key_exists($routeName, $this->routePerms)) {
            $this->getLogger()->warn(
                sprintf('New route [%s] has no permissions', $routeName)
            );

            return new ApiProblemResponse(new ApiProblem(403, 'Not Authorized'));
        }

        $method     = $event->getRequest()->getMethod();
        $permission = isset($this->routePerms[$routeName][$method]) ? $this->routePerms[$routeName][$method] : [null];
        $permission = !is_array($permission) ? [$permission] : $permission;
        $role       = $user->getRole();
        $assertion  = $this->getAssertion($event, $role, $permission);

        if (!$this->rbac->isGranted(null, null, $assertion)) {
            $this->getLogger()->alert(
                sprintf('An attempt was made to access route [%s] was made', $routeName)
            );

            return new ApiProblemResponse(new ApiProblem(403, 'Not Authorized'));
        }

        return null;
    }

    /**
     * @param MvcEvent $event
     * @param $role
     * @param $permission
     * @return AssertionInterface
     */
    protected function getAssertion(MvcEvent $event, $role, array $permission)
    {
        $assertion  = $event->getParam('assertion', new DefaultAssertion());

        if ($assertion instanceof AssertionInterface) {
            $assertion->setRole($role);
            $assertion->setPermission($permission);
        }

        return $assertion;
    }

    /**
     * Checks if the route is restricted
     *
     * @param MvcEvent $event
     * @return bool
     */
    protected function isRouteUnRestricted(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return true;
        }

        if ($request->getMethod() === HttpRequest::METHOD_OPTIONS) {
            return true;
        }

        return $this->isRouteOpen($event);
    }

    /**
     *
     * @param MvcEvent $event
     * @return string
     */
    protected function getRoleForGroup(MvcEvent $event)
    {
        $group = $event->getRouteMatch()->getParam('group_id', false);
        $orgId = $event->getRouteMatch()->getParam('org_id', false);
        if ($group === false && $orgId === false) {
            return 'logged_in';
        }

        $identity  = $this->authService->getIdentity();
        $foundRole = false;

        if ($group !== false) {
            $foundRole = $this->orgService->getRoleForGroup($group, $identity);
        } elseif ($orgId !== false) {
            $foundRole = $this->orgService->getRoleForOrg($orgId, $identity);
        }

        $foundRole = $foundRole === false ? 'logged_in' : $foundRole;

        if ($identity instanceof SecurityUser) {
            $identity->setRole($foundRole);
        }

        return $foundRole;
    }
}
