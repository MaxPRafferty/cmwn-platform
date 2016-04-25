<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\Hal\Entity;

/**
 * Class GroupRouteListener
 */
class GroupRouteListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * GroupRouteListener constructor.
     *
     * @param GroupServiceInterface $groupService
     */
    public function __construct(GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
        $this->listeners[] = $events->attach('*', MvcEvent::EVENT_RENDER, [$this, 'onRender'], 1000);
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
     * @return null|ApiProblem
     */
    public function onRoute(MvcEvent $event)
    {
        $route   = $event->getRouteMatch();
        $groupId = $route->getParam('group_id', false);

        if ($groupId === false) {
            return null;
        }

        try {
            $group = $this->groupService->fetchGroup($groupId);
        } catch (NotFoundException $notFound) {
            return new ApiProblem(404, 'Group not found');
        }

        $route->setParam('group', $group);
        return null;
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
}
