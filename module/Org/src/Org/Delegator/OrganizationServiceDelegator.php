<?php

namespace Org\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\HideDeletedEntitiesListener;
use Org\Service\OrganizationService;
use Org\Service\OrganizationServiceInterface;
use Org\OrganizationInterface;
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

    /**
     * @var OrganizationService
     */
    protected $realService;

    public function __construct(OrganizationService $service)
    {
        $this->realService = $service;
    }

    protected function attachDefaultListeners()
    {
        $hideListener = new HideDeletedEntitiesListener(['fetch.all.orgs'], ['fetch.org.post']);
        $hideListener->setEntityParamKey('org');

        $this->getEventManager()->attach($hideListener);
    }


    /**
     * Saves a organization
     *
     * If the org id is null, then a new organization is created
     *
     * @param OrganizationInterface $org
     * @return bool
     * @throws NotFoundException
     */
    public function saveOrg(OrganizationInterface $org)
    {
        $event    = new Event('save.org', $this->realService, ['org' => $org]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveOrg($org);

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
    public function fetchOrg($orgId)
    {
        $event    = new Event('fetch.org', $this->realService, ['org_id' => $orgId]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchOrg($orgId);
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
    public function deleteOrg(OrganizationInterface $org, $soft = true)
    {
        $event    = new Event('delete.org', $this->realService, ['org' => $org, 'soft' => $soft]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteOrg($org, $soft);
        $event  = new Event('delete.org.post', $this->realService, ['org' => $org, 'soft' => $soft]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where    = !$where instanceof PredicateInterface ? new Where($where) : $where;
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
}
