<?php

namespace Security\Authorization;

use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\Assertions\DefaultAssertion;
use Security\SecurityUser;
use Security\Service\SecurityOrgService;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class RouteListener
 */
class RouteListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $openRoutes = [];

    /**
     * @var array|mixed
     */
    protected $routePerms = [];

    /**
     * @var SecurityOrgService
     */
    protected $orgService;

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
        $this->openRoutes  = isset($config['open-routes']) ? $config['open-routes'] : [];
        $this->routePerms  = isset($config['route-permissions']) ? $config['route-permissions'] : [];
        $this->orgService  = $orgService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
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
    public function onRoute(MvcEvent $event)
    {
        if ($this->isRouteOpen($event)) {
            return;
        }

        if (!$this->authService->hasIdentity()) {
            return new ApiProblemResponse(new ApiProblem(401, 'Authentication failed'));
        }

        /** @var SecurityUser $user */
        $user = $this->authService->getIdentity();
        if ($user->isSuper()) {
            return;
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
            return new ApiProblemResponse(new ApiProblem(401, 'Not Authorized'));
        }

        $method     = $event->getRequest()->getMethod();
        $permission = isset($this->routePerms[$routeName][$method]) ? $this->routePerms[$routeName][$method] : null;
        $role       = $user->getRole();
        $assertion  = $this->getAssertion($event, $role, $permission);

        if (!$this->rbac->isGranted(null, null, $assertion)) {
            return new ApiProblemResponse(new ApiProblem(401, 'Not Authorized'));
        }

        return null;
    }

    /**
     * @param MvcEvent $event
     * @param $role
     * @param $permission
     * @return AssertionInterface
     */
    protected function getAssertion(MvcEvent $event, $role, $permission)
    {
        $assertion  = $event->getParam('assertion', new DefaultAssertion());

        if ($assertion instanceof AssertionInterface) {
            $assertion->setRole($role);
            $assertion->setPermission($permission);
        }

        return $assertion;
    }

    /**
     * Checks if the route is allowed to be accessed openly
     *
     * @param MvcEvent $event
     * @return bool
     */
    protected function isRouteOpen(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return true;
        }

        if ($request->getMethod() === HttpRequest::METHOD_OPTIONS) {
            return true;
        }

        $routeName = $event->getRouteMatch()->getMatchedRouteName();
        return in_array($routeName, $this->openRoutes);
    }

    /**
     *
     * @param MvcEvent $event
     * @return string
     */
    protected function getRoleForGroup(MvcEvent $event)
    {
        $group = $event->getRouteMatch()->getParam('group_id', false);

        if ($group === false) {
            return 'logged_in';
        }

        $identity  = $this->authService->getIdentity();
        $foundRole = $this->orgService->getRoleForGroup($group, $identity);
        $foundRole = $foundRole === false ? 'logged_in' : $foundRole;

        if ($identity instanceof SecurityUser) {
            $identity->setRole($foundRole);
        }

        return $foundRole;
    }
}
