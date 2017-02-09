<?php

namespace Org\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Org\Organization;
use Org\OrganizationInterface;
use Ramsey\Uuid\Uuid;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * A Service that handles organizations in a database
 */
class OrganizationService implements OrganizationServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $orgTableGateway;

    /**
     * @var ArraySerializable
     */
    protected $hydrator;

    /**
     * OrganizationService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->orgTableGateway = $gateway;
        $this->hydrator        = new ArraySerializable();
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, OrganizationInterface $prototype = null): AdapterInterface
    {
        $prototype = $prototype ?? new Organization();
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);
        $select    = new Select(['o' => $this->orgTableGateway->getTable()]);
        $select->where($where);
        $select->order(['o.title']);

        return new DbSelect(
            $select,
            $this->orgTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function createOrganization(OrganizationInterface $org): bool
    {
        $org->setUpdated(new \DateTime());
        $org->setCreated(new \DateTime());
        $org->setOrgId((string)Uuid::uuid1());
        $data = $org->getArrayCopy();

        $data['meta']   = Json::encode($data['meta']);
        unset($data['deleted']);
        unset($data['links']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware
        unset($data['scope']);

        $this->orgTableGateway->insert($data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateOrganization(OrganizationInterface $org): bool
    {
        $this->fetchOrganization($org->getOrgId());
        $org->setUpdated(new \DateTime());

        $data         = $org->getArrayCopy();
        $data['meta'] = Json::encode($data['meta']);
        unset($data['deleted']);
        unset($data['org_id']);
        unset($data['created']);
        unset($data['links']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware
        $this->orgTableGateway->update(
            $data,
            ['org_id' => $org->getOrgId()]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchOrganization(string $orgId, OrganizationInterface $prototype = null): OrganizationInterface
    {
        $prototype = $prototype ?? new Organization();
        $rowSet    = $this->orgTableGateway->select(['org_id' => $orgId]);
        $row       = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Organization not Found");
        }

        $this->hydrator->hydrate($row->getArrayCopy(), $prototype);

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function deleteOrganization(OrganizationInterface $org, bool $soft = true): bool
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

    /**
     * @inheritdoc
     */
    public function fetchGroupTypes(OrganizationInterface $organization): array
    {
        $where  = $this->createWhere(['organization_id' => $organization->getOrgId()]);
        $select = new Select();
        $select->columns([new Expression('DISTINCT(type) AS type')]);
        $select->from('groups');
        $select->where($where);

        $sql     = new Sql($this->orgTableGateway->getAdapter());
        $stmt    = $sql->prepareStatementForSqlObject($select);
        $results = $stmt->execute();

        $types = [];
        foreach ($results as $row) {
            $types[] = $row['type'];
        }

        sort($types);

        return array_unique($types);
    }

    /**
     * @inheritdoc
     */
    public function fetchOrgTypes(): array
    {
        $select = new Select();
        $select->columns([new Expression('DISTINCT(type) AS type')]);
        $select->from($this->orgTableGateway->getTable());

        $results = $this->orgTableGateway->selectWith($select);
        $types   = [];
        foreach ($results as $row) {
            $types[] = $row['type'];
        }

        sort($types);

        return array_unique($types);
    }
}
