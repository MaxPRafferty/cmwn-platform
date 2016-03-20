<?php

namespace Security\Authorization;

use Security\SecurityUser;
use Security\Service\SecurityOrgService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\Hal\View\HalJsonModel;

/**
 * Class RouteListener
 */
class RouteListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var array
     */
    protected $openRoutes = [];

    /**
     * @var array|mixed
     */
    protected $routePerms = [];

    /**
     * @var AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @var SecurityOrgService
     */
    protected $orgService;

    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * RouteListener constructor.
     *
     * @param array $config
     * @param AuthenticationServiceInterface $autService
     * @param SecurityOrgService $orgService
     * @param Rbac $rbac
     */
    public function __construct(
        array $config,
        AuthenticationServiceInterface $autService,
        SecurityOrgService $orgService,
        Rbac $rbac
    ) {
        $this->openRoutes  = isset($config['open-routes']) ? $config['open-routes'] : [];
        $this->routePerms  = isset($config['route-permissions']) ? $config['route-permissions'] : [];
        $this->authService = $autService;
        $this->orgService  = $orgService;
        $this->rbac        = $rbac;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onRender'], -10);
    }

    public function onRender(MvcEvent $event)
    {
        /** @var HalJsonModel $response */
        $response = $event->getResult();

        $payload = $response->getPayload();
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

        if (!$this->rbac->isGranted($user->getRole(), $permission)) {
            return new ApiProblemResponse(new ApiProblem(401, 'Not Authorized'));
        }

        return null;
    }

    /**
     * Checks if the route is allowed to be accessed openly
     *
     * @param MvcEvent $event
     * @return bool
     */
    protected function isRouteOpen(MvcEvent $event)
    {
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

        $foundRole = $this->orgService->getRoleForGroup($group, $this->authService->getIdentity());
        return $foundRole !== false ? 'logged_in' : $foundRole;
    }
}
