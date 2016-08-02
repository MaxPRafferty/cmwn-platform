<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Application\Exception\NotAuthorizedException;
use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
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
     * @var \Exception
     */
    protected $exception;

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
        $this->listeners[] = $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_RENDER, [$this, 'onRender'], 1000);
        $this->listeners[] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            PHP_INT_MAX
        );
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
}
