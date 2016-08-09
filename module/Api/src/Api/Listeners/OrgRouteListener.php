<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Application\Exception\NotFoundException;
use Org\OrganizationInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\Hal\Entity;

/**
 * Class OrgRouteListener
 *
 * @TODO Make this a shared listener
 */
class OrgRouteListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * orgRouteListener constructor.
     *
     * @param OrganizationServiceInterface $orgService
     */
    public function __construct(OrganizationServiceInterface $orgService)
    {
        $this->orgService = $orgService;
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender']);
    }

    /**
     * Injects the group type hal links when a organization is going to be rendered
     *
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        $payload = $event->getViewModel()->getVariable('payload');

        if (!$payload instanceof Entity) {
            return;
        }

        $realEntity = $payload->entity;

        if (!$realEntity instanceof OrganizationInterface) {
            return;
        }

        $types = $this->orgService->fetchGroupTypes($realEntity);
        foreach ($types as $type) {
            $payload->getLinks()->add(new GroupLink($type, null, $realEntity->getOrgId()));
        }

        // Add the generic group link to creating new groups
        $payload->getLinks()->add(new GroupLink());
    }
}
