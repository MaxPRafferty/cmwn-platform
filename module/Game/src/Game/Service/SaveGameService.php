<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\Date\DateTimeFactory;
use Application\Utils\ServiceTrait;
use Game\Game;
use Game\GameInterface;
use Game\SaveGame;
use Game\SaveGameInterface;
use User\PlaceHolder;
use User\UserInterface;
use Zend\Db\Adapter\Exception\InvalidQueryException;
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

        $user = new PlaceHolder();
        $user->setUserId($gameData->getUserId());

        $game = new Game();
        $game->setGameId($gameData->getGameId());

        try {
            $this->tableGateway->insert($data);
            return true;
        } catch (InvalidQueryException $exception) {
            if ($exception->getPrevious()->getCode() !='23000') {
                throw $exception;
            }
        }

        unset($data['game_id'], $data['user_id']);
        $updated = $this->tableGateway->update(
            $data,
            ['user_id' => $user->getUserId(), 'game_id' => $game->getGameId()]
        );

        return $updated == 1;
    }

    /**
     * @inheritdoc
     */
    public function deleteSaveForUser(UserInterface $user, GameInterface $game): bool
    {
        $this->tableGateway->delete([
            'user_id' => $user->getUserId(),
            'game_id' => $game->getGameId(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchSaveGameForUser(
        UserInterface $user,
        GameInterface $game,
        $where = null,
        SaveGameInterface $prototype = null
    ): SaveGameInterface {
        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('user_id', '=', $user->getUserId()));
        $where->addPredicate(new Operator('game_id', '=', $game->getGameId()));

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
        UserInterface $user,
        $where = null,
        SaveGameInterface $prototype = null
    ): AdapterInterface {
        $where     = $this->createWhere($where);
        $prototype = $prototype ?? new SaveGame();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        $where->addPredicate(new Operator('user_id', '=', $user->getUserId()));
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
