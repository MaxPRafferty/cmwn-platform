<?php

namespace Api\Listeners;

use Application\Exception\NotFoundException;
use Group\Service\GroupServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;

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
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
    }

    /**
     * @param MvcEvent $event
     * @return null|ApiProblem
     */
    public function onRoute(MvcEvent $event)
    {
        $route = $event->getRouteMatch();
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
}
