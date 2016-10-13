<?php

namespace Org\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\HideDeletedEntitiesListener;
use Application\Utils\ServiceTrait;
use Org\Service\OrganizationService;
use Org\Service\OrganizationServiceInterface;
use Org\OrganizationInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class OrganizationServiceDelegator
 *
 * @package Organization\Delegator
 */
class OrganizationServiceDelegator implements OrganizationServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;
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
     * OrganizationServiceDelegator constructor.
     * @param OrganizationService $service
     */
    public function __construct(OrganizationService $service)
    {
        $this->realService = $service;
    }

    /**
     * Attaches the Hides Deleted Listeners
     */
    protected function attachDefaultListeners()
    {
        $hideListener = new HideDeletedEntitiesListener(['fetch.all.orgs'], ['fetch.org.post'], 'o');
        $hideListener->setEntityParamKey('org');

        $this->getEventManager()->attach($hideListener);
    }

    /**
     * @param OrganizationInterface $org
     * @return mixed
     */
    public function createOrganization(OrganizationInterface $org)
    {
        $event    = new Event('save.new.org', $this->realService, ['org' => $org]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->createOrganization($org);

        $event    = new Event('save.new.org.post', $this->realService, ['org' => $org]);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * @param OrganizationInterface $org
     * @return mixed
     */
    public function updateOrganization(OrganizationInterface $org)
    {
        $event    = new Event('save.org', $this->realService, ['org' => $org]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->updateOrganization($org);

        $event    = new Event('save.org.post', $this->realService, ['org' => $org]);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Fetches one organization from the DB using the id
     *
     * @param $orgId
     * @return OrganizationInterface
     * @throws NotFoundException
     */
    public function fetchOrganization($orgId)
    {
        $event    = new Event('fetch.org', $this->realService, ['org_id' => $orgId]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchOrganization($orgId);
        $event    = new Event('fetch.org.post', $this->realService, ['org_id' => $orgId, 'org' => $return]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Deletes a org from the database
     *
     * Soft deletes unless soft is false
     *
     * @param OrganizationInterface $org
     * @param bool $soft
     * @return bool
     */
    public function deleteOrganization(OrganizationInterface $org, $soft = true)
    {
        $event    = new Event('delete.org', $this->realService, ['org' => $org, 'soft' => $soft]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteOrganization($org, $soft);
        $event  = new Event('delete.org.post', $this->realService, ['org' => $org, 'soft' => $soft]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param null|\Zend\Db\Sql\Predicate\PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where    = $this->createWhere($where);
        $event    = new Event(
            'fetch.all.orgs',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $paginate, $prototype);
        $event    = new Event(
            'fetch.all.orgs.post',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype, 'orgs' => $return]
        );
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Fetches the type of groups that are in this organization
     *
     * @param $organization
     * @return string[]
     */
    public function fetchGroupTypes($organization)
    {
        $event    = new Event('fetch.org.group.types', $this->realService, ['organization' => $organization]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchGroupTypes($organization);
        $event->setName('fetch.org.group.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Fetches all the types of organizations
     *
     * @return string[]
     */
    public function fetchOrgTypes()
    {
        $event    = new Event('fetch.org.types', $this->realService);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchOrgTypes();
        $event->setName('fetch.org.types.post');
        $event->setParam('results', $return);
        $this->getEventManager()->trigger($event);
        return $return;
    }
}
