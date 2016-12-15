<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Game\Game;
use Game\GameInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GameService
 * @package Game\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GameService implements GameServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $gameTableGateway;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->gameTableGateway = $gateway;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select    = new Select(['g' => $this->gameTableGateway->getTable()]);
            $select->where($where);
            $select->order(['g.title']);

            return new DbSelect(
                $select,
                $this->gameTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->gameTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * Fetches one game from the DB using the id
     *
     * @param $gameId
     * @return GameInterface
     * @throws NotFoundException
     */
    public function fetchGame($gameId)
    {
        $where = new Where();
        $where->equalTo('game_id', $gameId);
        $where->isNull('deleted');
        $rowSet = $this->gameTableGateway->select($where);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Game not Found");
        }

        return new Game((array) $row);
    }

    /**
     * @inheritdoc
     */
    public function saveGame(GameInterface $game)
    {
        $game->setUpdated(new \DateTime());
        $data = $game->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);

        unset($data['deleted']);

        $this->fetchGame($game->getGameId());

        $this->gameTableGateway->update(
            $data,
            ['game_id' => $game->getGameId()]
        );
    }

    /**
     * @inheritdoc
     */
    public function createGame(GameInterface $game)
    {
        $game->setCreated(new \DateTime());
        $title = $game->getTitle();

        $gameId = str_replace(' ', '-', strtolower($title));

        $game->setGameId($gameId);
        $game->setUpdated(new \DateTime());

        $data = $game->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);

        $data['created'] = $game->getCreated()->format(\DateTime::ISO8601);
        $data['updated'] = $data['created'];
        $this->gameTableGateway->insert($data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteGame(GameInterface $game, $soft = true)
    {
        $this->fetchGame($game->getGameId());

        if ($soft) {
            $game->setDeleted(new \DateTime());

            $this->gameTableGateway->update(
                ['deleted' => $game->getDeleted()->format(\DateTime::ISO8601)],
                ['game_id' => $game->getGameId()]
            );

            return true;
        }

        $this->gameTableGateway->delete(['game_id' => $game->getGameId()]);

        return true;
    }
}
