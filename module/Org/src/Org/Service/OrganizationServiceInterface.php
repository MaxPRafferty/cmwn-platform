<?php

namespace Org\Service;

use Application\Exception\NotFoundException;
use Org\OrganizationInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;


/**
 * Interface OrganizationServiceInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface OrganizationServiceInterface
{
    /**
     * Fetches all Organizations
     *
     * Returns a pagination adapter by default
     *
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null);

    /**
     * Saves an Organization
     *
     * If the org_id is null, then a new Organization is created
     *
     * @param OrganizationInterface $org
     * @return bool
     * @throws NotFoundException
     */
    public function saveOrg(OrganizationInterface $org);

    /**
     * Fetches one Organization from the DB using the id
     *
     * @param $orgId
     * @return OrganizationInterface
     * @throws NotFoundException
     */
    public function fetchOrg($orgId);

    /**
     * Deletes an Organization from the database
     *
     * Soft deletes unless soft is false
     *
     * @param OrganizationInterface $org
     * @param bool $soft
     * @return bool
     */
    public function deleteOrg(OrganizationInterface $org, $soft = true);
}
