<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Api\Links\GroupResetLink;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\Service\SecurityOrgService;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\Hal\Entity;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class GroupRouteListener
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class GroupRouteListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var SecurityOrgService
     */
    protected $orgService;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * GroupRouteListener constructor.
     *
     * @param GroupServiceInterface $groupService
     * @param SecurityOrgService $orgService
     */
    public function __construct(GroupServiceInterface $groupService, SecurityOrgService $orgService)
    {
        $this->groupService = $groupService;
        $this->orgService = $orgService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_RENDER, [$this, 'onRender'], 1000);
        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            PHP_INT_MAX
        );
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'onEntityRender']);
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
     * @return ApiProblem
     */
    public function onDispatch()
    {
        if ($this->exception !== null) {
            return new ApiProblem(404, 'Not Found');
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        $payload = $event->getViewModel()->getVariable('payload');

        if (!$payload instanceof Entity) {
            return;
        }

        $realEntity = $payload->entity;

        if (!$realEntity instanceof GroupInterface) {
            return;
        }

        $types = $this->groupService->fetchChildTypes($realEntity);
        foreach ($types as $type) {
            $payload->getLinks()->add(new GroupLink($type, $realEntity->getGroupId()));
        }
    }

    /**
     * Adds a GroupResetLink if permission is granted to the user
     * @param Event $event
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function onEntityRender(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return;
        }

        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof GroupInterface) {
            return;
        }

        if (!$realEntity instanceof LinkCollectionAwareInterface) {
            return;
        }

        try {
            $user = $this->authService->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        $role = $user->isSuper() ? $user->getRole() : $this->orgService->getRoleForGroup($realEntity, $user);
        if ($this->getRbac()->isGranted($role, 'reset.group.code')) {
            $realEntity->getLinks()->add(new GroupResetLink($realEntity));
        }
    }
}
