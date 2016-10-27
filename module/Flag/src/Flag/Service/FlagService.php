<?php

namespace Flag\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Flag\Flag;
use Flag\FlagHydrator;
use Flag\FlagInterface;
use Ramsey\Uuid\Uuid;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FlagService
 * @package Flag\Service
 */
class FlagService implements FlagServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $flagTableGateway;

    /**
     * @var FlagHydrator
     */
    protected $flagHydrator;

    /**
     * FlagService constructor.
     * @param TableGateway $flagTableGateway
     * @param FlagHydrator $flagHydrator
     */
    public function __construct($flagTableGateway, $flagHydrator)
    {
        $this->flagTableGateway = $flagTableGateway;
        $this->flagHydrator = $flagHydrator;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, $prototype = null)
    {
        $where = $this->createWhere($where);
        $prototype = $prototype === null ? new Flag() : $prototype;
        $this->flagHydrator->setPrototype($prototype);
        $resultSet = new HydratingResultSet($this->flagHydrator, $prototype);

        $select = new Select(['ft' => $this->flagTableGateway->getTable()]);
        $select->where($where);
        return new DbSelect(
            $select,
            $this->flagTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchFlag($flagId, $prototype = null)
    {
        $where = new Where();
        $prototype = $prototype === null ? new Flag() : $prototype;
        $this->flagHydrator->setPrototype($prototype);

        $where->addPredicate(new Operator('flag_id', '=', $flagId));

        $rowSet = $this->flagTableGateway->select($where);
        $row = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("No Flagged Image Found");
        }

        $this->flagHydrator->hydrate($row->getArrayCopy(), $prototype);
        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function saveFlag(FlagInterface $flag)
    {
        $flagger = $flag->getFlagger() instanceof UserInterface ? $flag->getFlagger()->getUserId(): $flag->getFlagger();
        $flaggee = $flag->getFlaggee() instanceof UserInterface ? $flag->getFlaggee()->getUserId(): $flag->getFlaggee();

        $flag->setFlagId(Uuid::uuid1());
        $flagData = $flag->getArrayCopy();
        $flagData['flagger'] = $flagger;
        $flagData['flaggee'] = $flaggee;
        $this->flagTableGateway->insert(
            $flagData
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateFlag(FlagInterface $flag)
    {
        $flagger = $flag->getFlagger() instanceof UserInterface ? $flag->getFlagger()->getUserId(): $flag->getFlagger();
        $flaggee = $flag->getFlaggee() instanceof UserInterface ? $flag->getFlaggee()->getUserId(): $flag->getFlaggee();

        $data = $flag->getArrayCopy();
        $data['flagger'] = $flagger;
        $data['flaggee'] = $flaggee;
        $this->flagTableGateway->update($data, ['flag_id' => $flag->getFlagId()]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteFlag(FlagInterface $flag)
    {
        $this->fetchFlag($flag->getFlagId());

        $this->flagTableGateway->delete(['flag_id' => $flag->getFlagId()]);
        return true;
    }
}
