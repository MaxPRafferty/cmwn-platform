<?php

namespace Org\Service;

use Application\Exception\NotFoundException;
use Org\OrganizationInterface;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Interface OrganizationServiceInterface
 */
interface OrganizationServiceInterface
{
    /**
     * Fetches all Organizations
     *
     * Returns a pagination adapter by default
     *
     * @param null|PredicateInterface|array $where
     * @param null|OrganizationInterface $prototype
     *
     * @return AdapterInterface
     */
    public function fetchAll($where = null, OrganizationInterface $prototype = null): AdapterInterface;

    /**
     * Fetches one Organization from the DB using the id
     *
     * @param string $orgId
     * @param OrganizationInterface $prototype
     *
     * @return OrganizationInterface
     * @throws NotFoundException
     */
    public function fetchOrganization(string $orgId, OrganizationInterface $prototype = null): OrganizationInterface;

    /**
     * Deletes an Organization from the database
     *
     * Soft deletes unless soft is false
     *
     * @param OrganizationInterface $org
     * @param bool $soft
     *
     * @return bool
     */
    public function deleteOrganization(OrganizationInterface $org, bool $soft = true): bool;

    /**
     * Saves an Organization
     *
     * If the org_id is null, then a new Organization is created
     *
     * @param OrganizationInterface $org
     *
     * @return bool
     */
    public function createOrganization(OrganizationInterface $org): bool;

    /**
     * Saves an existing Organization
     *
     * @param OrganizationInterface $org
     *
     * @return bool
     * @throws NotFoundException
     */
    public function updateOrganization(OrganizationInterface $org): bool;

    /**
     * Fetches the type of groups that are in this organization
     *
     * @param OrganizationInterface $organization
     *
     * @return string[]
     */
    public function fetchGroupTypes(OrganizationInterface $organization): array;

    /**
     * Fetches all the types of organizations registered in the system
     *
     * @return string[]
     */
    public function fetchOrgTypes(): array;
}
