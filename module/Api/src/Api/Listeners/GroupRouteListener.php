<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\Hal\Entity;

/**
 * Class GroupRouteListener
 */
class GroupRouteListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

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
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     * @todo Make this part of the shared listener
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender'], 1000);
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
