<?php

namespace Security\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\Assertion\AssertionInterface;
use Security\Authorization\Assertion\DefaultAssertion;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\Service\SecurityGroupServiceInterface;
use Security\Utils\OpenRouteTrait;
use Security\SecurityUser;
use Security\Service\SecurityUserService;
use Security\Service\SecurityOrgServiceInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Log\LoggerAwareInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * @todo port this to rules engine
 * Class RouteListener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var SecurityGroupServiceInterface
     */
    protected $securityGroupService;

    /**
     * @var SecurityOrgServiceInterface
     */
    protected $orgService;

    /**
     * @var SecurityUserService
     */
    protected $groupService;

    /**
     * RouteListener constructor.
     * @param array $config
     * @param SecurityOrgServiceInterface $orgService
     * @param SecurityGroupServiceInterface $securityGroupService
     * @param SecurityUserService $groupService
     */
    public function __construct(
        array $config,
        SecurityOrgServiceInterface $orgService,
        SecurityGroupServiceInterface $securityGroupService,
        SecurityUserService $groupService
    ) {
        $config             = $config['cmwn-security'] ?? [];
        $this->routePerms   = isset($config['route-permissions']) ? $config['route-permissions'] : [];
        $this->securityGroupService = $securityGroupService;
        $this->orgService   = $orgService;
        $this->groupService = $groupService;
        $this->setOpenRoutes(isset($config['open-routes']) ? $config['open-routes'] : []);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach(
            Application::class,
            MvcEvent::EVENT_ROUTE,
            $this,
            -2
        );
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        $events->detach($this, Application::class);
    }

    /**
     * @param MvcEvent $event
     *
     * @return null|ApiProblemResponse
     */
    public function __invoke(MvcEvent $event)
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
        try {
            $user = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePass) {
            // todo create a new listener that will check for change password user and send this response
            if ($event->getRouteMatch()->getMatchedRouteName() !== 'api.rest.update-password') {
                return new ApiProblemResponse(new ApiProblem(401, 'RESET_PASSWORD'));
            }

            $user = $changePass->getUser();
        }

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
     *
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
     *
     * @return AssertionInterface
     */
    protected function getAssertion(MvcEvent $event, $role, array $permission)
    {
        $assertion = $event->getParam('assertion', new DefaultAssertion());

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
     *
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
     *
     * @return string
     */
    protected function getRoleForGroup(MvcEvent $event)
    {
        $group  = $event->getRouteMatch()->getParam('group_id', false);
        $orgId  = $event->getRouteMatch()->getParam('org_id', false);
        $userId = $event->getRouteMatch()->getParam('user_id', false);
        try {
            $identity = $this->authService->getIdentity();
        } catch (ChangePasswordException $reset) {
            $identity = $reset->getUser();
        }

        switch (true) {
            case $group !== false:
                $foundRole = $this->securityGroupService->getRoleForGroup($group, $identity);
                break;

            case $orgId !== false:
                $foundRole = $this->orgService->getRoleForOrg($orgId, $identity);
                break;

            case $userId !== false:
                $foundRole = $this->groupService->fetchRelationshipRole($identity, $userId);
                break;

            default:
                // Become "me" when no org, group or user
                // This means that we are hitting /user, /group or /org
                $foundRole = 'me.' . strtolower($identity->getType());
        }

        if ($identity instanceof SecurityUser) {
            $identity->setRole($foundRole);
        }

        return $foundRole;
    }
}
