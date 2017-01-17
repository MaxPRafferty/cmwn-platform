<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Org\OrganizationInterface;
use Org\Service\OrganizationServiceInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use ZF\Hal\Entity;

/**
 * Class OrgRouteListener
 *
 * @TODO Make this a rule
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
     * @inheritDoc
     */
    public function attach(EventManagerInterface $events, $priority = 1)
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

        $realEntity = $payload->getEntity();

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
