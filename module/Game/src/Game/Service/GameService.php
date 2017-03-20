<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Game\Game;
use Game\GameInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
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

    protected static $flagMap = [
        'global'      => GameInterface::GAME_GLOBAL,
        'featured'    => GameInterface::GAME_FEATURED,
        'coming_soon' => GameInterface::GAME_COMING_SOON,
    ];

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
        $data          = array_diff_key($game->getArrayCopy(), static::$flagMap);
        $data['meta']  = Json::encode($game->getMeta());
        $data['flags'] = $game->getFlags();
        $data['uris']  = Json::encode($game->getUris());

        return $data;
    }

    /**
     * @param $where
     *
     * @return PredicateInterface|PredicateSet|Where
     */
    public function createWhere($where)
    {
        if (!empty($where) && is_array($where)) {
            // pull out the flag keys
            $flagWhere = array_intersect_key($where, array_flip(['global', 'coming_soon', 'featured']));

            // now remove the flag keys
            $where = array_diff_key($where, $flagWhere);
            $this->aliasKeys($where);
            $set = new PredicateSet();
            $set->addPredicates($where);

            // Add the flags as an or
            array_walk($flagWhere, function ($value, $flag) use (&$set) {
                $bit = static::$flagMap[$flag] ?? null;
                if ($bit === null) {
                    // skip if flag not defined
                    return;
                }

                $expression = $value
                    ? new Expression('g.flags & ? = ?', $bit, $bit)
                    : new Expression('g.flags & ? != ?', $bit, $bit);

                $set->orPredicate($expression);
            });

            $where = $set;
        }

        return !$where instanceof PredicateInterface ? new Where() : $where;
    }

    /**
     * Any class using this trait can return the the table alias here
     *
     * @return string
     */
    public function getAlias(): string
    {
        return 'g';
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
        $select->order(['title']);

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
    public function saveGame(GameInterface $game): bool
    {
        $game->setUpdated(new \DateTime());

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
