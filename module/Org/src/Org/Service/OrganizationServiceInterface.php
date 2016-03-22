<?php

namespace Org\Service;

use Application\Exception\NotFoundException;
use Org\OrganizationInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
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
     * Fetches one Organization from the DB using the id
     *
     * @param $orgId
     * @return OrganizationInterface
     * @throws NotFoundException
     */
    public function fetchOrganization($orgId);

    /**
     * Deletes an Organization from the database
     *
     * Soft deletes unless soft is false
     *
     * @param OrganizationInterface $org
     * @param bool $soft
     * @return bool
     */
    public function deleteOrganization(OrganizationInterface $org, $soft = true);

    /**
     * Saves an Organization
     *
     * If the org_id is null, then a new Organization is created
     *
     * @param OrganizationInterface $org
     * @return bool
     */
    public function createOrganization(OrganizationInterface $org);

    /**
     * Saves an existing Organization
     *
     * @param OrganizationInterface $org
     * @return bool
     * @throws NotFoundException
     */
    public function updateOrganization(OrganizationInterface $org);

    /**
     * SELECT o.*
     * FROM organizations o
     *   INNER JOIN groups AS g ON g.organization_id = o.org_id
     *   LEFT JOIN user_groups AS ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = '87512ab4-f039-11e5-96b2-0800274f2cef';
     *
     * @param string|UserInterface $user
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAllForUser($user, $where = null, $paginate = true, $prototype = null);
}
