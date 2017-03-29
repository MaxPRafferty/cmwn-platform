<?php

namespace Org\Delegator;

use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Org\Service\OrganizationService;
use Org\Service\OrganizationServiceInterface;
use Org\OrganizationInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * A Delegator that will dispatch events for the OrganizationService
 */
class OrganizationServiceDelegator implements OrganizationServiceInterface
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $eventIdentifier = 'Org\Service\OrganizationServiceInterface';

    /**
     * @var OrganizationService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * OrganizationServiceDelegator constructor.
     *
     * @param OrganizationService $service
     * @param EventManagerInterface $events
     */
    public function __construct(OrganizationService $service, EventManagerInterface $events)
    {
        $this->realService = $service;
        $this->events      = $events;
        $this->events->addIdentifiers(array_merge(
            [OrganizationServiceInterface::class, static::class, OrganizationService::class],
            $events->getIdentifiers()
        ));
        $hideListener = new HideDeletedEntitiesListener(['fetch.all.orgs'], ['fetch.org.post'], 'o');
        $hideListener->setEntityParamKey('org');
        $hideListener->attach($this->events);
    }

    /**
     * Returns the event manager
     */
    public function getEventManager(): EventManagerInterface
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function createOrganization(OrganizationInterface $org): bool
    {
        $event = new Event('save.new.org', $this->realService, ['org' => $org]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->createOrganization($org);
        } catch (\Throwable $exception) {
            $event->setName('save.new.org.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('save.new.org.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function updateOrganization(OrganizationInterface $org): bool
    {
        $event = new Event('save.org', $this->realService, ['org' => $org]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->updateOrganization($org);
        } catch (\Throwable $exception) {
            $event->setName('save.org.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('save.org.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchOrganization(string $orgId, OrganizationInterface $prototype = null): OrganizationInterface
    {
        $event = new Event('fetch.org', $this->realService, ['org_id' => $orgId]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchOrganization($orgId, $prototype);
        } catch (\Throwable $exception) {
            $event->setName('fetch.org.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.org.post');
        $event->setParam('org', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteOrganization(OrganizationInterface $org, bool $soft = true): bool
    {
        $event = new Event('delete.org', $this->realService, ['org' => $org, 'soft' => $soft]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->deleteOrganization($org, $soft);
        } catch (\Throwable $exception) {
            $event->setName('delete.org.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('delete.org.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, OrganizationInterface $prototype = null): AdapterInterface
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.orgs',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchAll($where, $prototype);
        } catch (\Throwable $exception) {
            $event->setName('fetch.all.orgs.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.all.orgs.post');
        $event->setParam('orgs', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchGroupTypes(OrganizationInterface $organization): array
    {
        $event = new Event('fetch.org.group.types', $this->realService, ['organization' => $organization]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchGroupTypes($organization);
        } catch (\Throwable $exception) {
            $event->setName('fetch.org.group.types.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.org.group.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchOrgTypes(): array
    {
        $event = new Event('fetch.org.types', $this->realService);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchOrgTypes();
        } catch (\Throwable $exception) {
            $event->setName('fetch.org.types.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.org.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
