<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\Date\DateTimeFactory;
use Application\Utils\ServiceTrait;
use Game\GameInterface;
use Game\SaveGame;
use Game\SaveGameInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Saves game data to a user in a db
 */
class SaveGameService implements SaveGameServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var ArraySerializable
     */
    protected $hydrator;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->tableGateway = $gateway;
        $this->hydrator     = new ArraySerializable();
    }

    /**
     * @inheritdoc
     */
    public function saveGame(SaveGameInterface $gameData): bool
    {
        $gameData->setCreated(new \DateTime());
        $data = [
            'game_id' => $gameData->getGameId(),
            'user_id' => $gameData->getUserId(),
            'data'    => Json::encode($gameData->getData()),
            'version' => $gameData->getVersion(),
            'created' => DateTimeFactory::formatForMysql($gameData->getCreated()),
        ];

        $this->deleteSaveForUser($gameData->getUserId(), $gameData->getGameId());
        $this->tableGateway->insert($data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteSaveForUser($user, $game): bool
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $gameId = $game instanceof GameInterface ? $game->getGameId() : $game;

        $this->tableGateway->delete(['user_id' => $userId, 'game_id' => $gameId]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchSaveGameForUser(
        $user,
        $game,
        SaveGameInterface $prototype = null,
        $where = null
    ): SaveGameInterface {
        $where  = $this->createWhere($where);
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $gameId = $game instanceof GameInterface ? $game->getGameId() : $game;

        $where->addPredicate(new Operator('user_id', '=', $userId));
        $where->addPredicate(new Operator('game_id', '=', $gameId));

        $rowSet = $this->tableGateway->select($where);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("No Save game Found");
        }

        $prototype = $prototype ?? new SaveGame();
        $this->hydrator->hydrate((array)$row, $prototype);

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllSaveGamesForUser(
        $user,
        $where = null,
        SaveGameInterface $prototype = null
    ): AdapterInterface {
        $where     = $this->createWhere($where);
        $userId    = $user instanceof UserInterface ? $user->getUserId() : $user;
        $prototype = $prototype ?? new SaveGame();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        $where->addPredicate(new Operator('user_id', '=', $userId));
        $select = new Select(['sg' => $this->tableGateway->getTable()]);
        $select->where($where);
        $select->order("sg.created ASC");

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchAllSaveGameData($where = null, SaveGameInterface $prototype = null): AdapterInterface
    {
        $where     = $this->createWhere($where);
        $prototype = $prototype ?? new SaveGame();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        $select = new Select(['sg' => $this->tableGateway->getTable()]);
        $select->where($where);
        $select->order("sg.created DESC");

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }
}
