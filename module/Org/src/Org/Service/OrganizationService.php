<?php

namespace Org\Service;

use Application\Exception\NotFoundException;
use Org\Organization;
use Org\OrganizationInterface;
use Ramsey\Uuid\Uuid;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
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
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = !$where instanceof PredicateInterface ? new Where($where) : $where;
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select    = new Select($this->orgTableGateway->getTable());
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
     * Saves an Organization
     *
     * If the org_id is null, then a new Organization is created
     *
     * @param OrganizationInterface $org
     * @return bool
     * @throws NotFoundException
     */
    public function saveOrg(OrganizationInterface $org)
    {
        $new = empty($org->getOrgId());
        $org->setUpdated(new \DateTime());
        $data = $org->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);

        unset($data['password']);
        unset($data['deleted']);

        if ($new) {
            $org->setCreated(new \DateTime());
            $org->setOrgId(Uuid::uuid1());

            $data['org_id'] = $org->getOrgId();
            $data['created'] = $org->getCreated()->format(\DateTime::ISO8601);

            $this->orgTableGateway->insert($data);
            return true;
        }

        $this->fetchOrg($org->getOrgId());

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
    public function fetchOrg($orgId)
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
    public function deleteOrg(OrganizationInterface $org, $soft = true)
    {
        $this->fetchOrg($org->getOrgId());

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
