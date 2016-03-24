<?php

namespace Org\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Org\Organization;
use Org\OrganizationInterface;
use Ramsey\Uuid\Uuid;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class OrganizationService
 * @package Org\Service
 */
class OrganizationService implements OrganizationServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $orgTableGateway;

    public function __construct(TableGateway $gateway)
    {
        $this->orgTableGateway = $gateway;
    }

    /**
     * Fetches all Organizations
     *
     * Returns a pagination adapter by default
     *
     * @param null|\Zend\Db\Sql\Predicate\PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select    = new Select(['o' => $this->orgTableGateway->getTable()]);
            $select->where($where);
            return new DbSelect(
                $select,
                $this->orgTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->orgTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * SELECT o.*
     * FROM organizations o
     *   INNER JOIN groups AS g ON g.organization_id = o.org_id
     *   LEFT JOIN user_groups AS ug ON ug.group_id = g.group_id
     * WHERE ug.user_id = '87512ab4-f039-11e5-96b2-0800274f2cef';
     *
     * @param string|UserInterface $user
     * @param null|\Zend\Db\Sql\Predicate\PredicateInterface|array $where
     * @param bool $paginate
     * @param null $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAllForUser($user, $where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $userId    = $user instanceof UserInterface ? $user->getUserId() : $user;
        $select    = new Select();
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        $select->columns([Select::SQL_STAR], 'o');
        $select->from(['g'  => 'groups']);
        $select->join(['ug' => 'user_groups'], 'ug.group_id = g.group_id', [], Select::JOIN_LEFT);
        $where->addPredicate(new Operator(new Expression('ug.user_id'), '=', $userId));
        $select->where($where);

        if ($paginate) {
            return new DbSelect(
                $select,
                $this->orgTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->orgTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * Saves an Organization
     *
     * If the org_id is null, then a new Organization is created
     *
     * @param OrganizationInterface $org
     * @return bool
     */
    public function createOrganization(OrganizationInterface $org)
    {
        $org->setUpdated(new \DateTime());
        $org->setCreated(new \DateTime());
        $org->setOrgId((string) Uuid::uuid1());
        $data = $org->getArrayCopy();

        $data['meta']    = Json::encode($data['meta']);
        $data['org_id']  = $org->getOrgId();
        unset($data['deleted']);
        unset($data['scope']);

        $this->orgTableGateway->insert($data);
        return true;
    }

    /**
     * Saves an existing Organization
     *
     * @param OrganizationInterface $org
     * @return bool
     * @throws NotFoundException
     */
    public function updateOrganization(OrganizationInterface $org)
    {
        $this->fetchOrganization($org->getOrgId());
        $org->setUpdated(new \DateTime());

        $data         = $org->getArrayCopy();
        $data['meta'] = Json::encode($data['meta']);
        unset($data['deleted']);

        $this->orgTableGateway->update(
            $data,
            ['org_id' => $org->getOrgId()]
        );

        return true;
    }

    /**
     * Fetches one Organization from the DB using the id
     *
     * @param $orgId
     * @return OrganizationInterface
     * @throws NotFoundException
     */
    public function fetchOrganization($orgId)
    {
        $rowset = $this->orgTableGateway->select(['org_id' => $orgId]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("Organization not Found");
        }

        return new Organization((array) $row);
    }

    /**
     * Deletes an Organization from the database
     *
     * Soft deletes unless soft is false
     *
     * @param OrganizationInterface $org
     * @param bool $soft
     * @return bool
     */
    public function deleteOrganization(OrganizationInterface $org, $soft = true)
    {
        $this->fetchOrganization($org->getOrgId());

        if ($soft) {
            $org->setDeleted(new \DateTime());

            $this->orgTableGateway->update(
                ['deleted' => $org->getDeleted()->format(\DateTime::ISO8601)],
                ['org_id' => $org->getOrgId()]
            );

            return true;
        }

        $this->orgTableGateway->delete(['org_id' => $org->getOrgId()]);
        return true;
    }
}
