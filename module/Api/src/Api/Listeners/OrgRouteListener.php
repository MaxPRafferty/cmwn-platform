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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'onRender']);
    }

    /**
     * Injects the Organization into the route params when org_id is set on the route
     *
     * Will quickly 404 to save on other calls
     *
     * @param MvcEvent $event
     * @return null|ApiProblem
     */
    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            return null;
        }

        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return null;
        }

        $route   = $event->getRouteMatch();
        $orgId = $route->getParam('org_id', false);

        if ($orgId === false) {
            return null;
        }

        try {
            $org = $this->orgService->fetchOrganization($orgId);
        } catch (NotFoundException $notFound) {
            return new ApiProblem(404, 'org not found');
        }

        $route->setParam('org', $org);
        return null;
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
    }
}
