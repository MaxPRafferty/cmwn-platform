<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\Date\DateTimeFactory;
use Game\Game;
use Game\GameInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * A Game Service that will save the game to a Mysql Database
 */
class GameService implements GameServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $gameTableGateway;

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
        $this->gameTableGateway = $gateway;
        $this->hydrator         = new ArraySerializable();
    }

    /**
     * Transforms the game data into an array for upsert into the DB
     *
     * @param GameInterface $game
     *
     * @return array
     */
    protected function getDataForDb(GameInterface $game): array
    {
        return [
            'game_id'     => $game->getGameId(),
            'title'       => $game->getTitle(),
            'description' => $game->getDescription(),
            'meta'        => Json::encode($game->getMeta()),
            'flags'       => $game->getFlags(),
            'uris'        => Json::encode($game->getUris()),
            'sort_order'  => $game->getSortOrder(),
            'created'     => DateTimeFactory::formatForMysql($game->getCreated()),
            'updated'     => DateTimeFactory::formatForMysql($game->getUpdated()),
            'deleted'     => DateTimeFactory::formatForMysql($game->getDeleted()),
        ];
    }


    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, GameInterface $prototype = null, bool $deleted = false): AdapterInterface
    {
        $prototype = $prototype ?? new Game();
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);
        $select    = new Select(['g' => $this->gameTableGateway->getTable()]);
        $select->where($where);
        $select->order(['sort_order', 'title']);

        return new DbSelect(
            $select,
            $this->gameTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchGame(string $gameId, GameInterface $prototype = null): GameInterface
    {
        $prototype = $prototype ?? new Game();
        $where     = $this->createWhere(['game_id' => $gameId]);
        $rowSet    = $this->gameTableGateway->select($where);
        $row       = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Game not Found");
        }

        $this->hydrator->hydrate($row->getArrayCopy(), $prototype);

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function saveGame(GameInterface $game, bool $removeSoft = false): bool
    {
        $game->setUpdated(new \DateTime());
        if ($removeSoft) {
            $game->setDeleted(null);
        }

        $this->gameTableGateway->update(
            $this->getDataForDb($game),
            ['game_id' => $game->getGameId()]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function createGame(GameInterface $game): bool
    {
        $game->setCreated(new \DateTime());
        $game->setUpdated(new \DateTime());

        $this->gameTableGateway->insert($this->getDataForDb($game));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteGame(GameInterface $game, bool $soft = true): bool
    {
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
