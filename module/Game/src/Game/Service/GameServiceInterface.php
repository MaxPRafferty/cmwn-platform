<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Game\GameInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface GameServiceInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface GameServiceInterface
{

    /**
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $prototype = null);

    /**
     * Fetches one game from the DB using the id
     *
     * @param $gameId
     * @return GameInterface
     * @throws NotFoundException
     */
    public function fetchGame($gameId);

    /**
     * @param GameInterface $game
     * @return bool
     * @throws NotFoundException
     */
    public function saveGame(GameInterface $game);

    /**
     * @param GameInterface $game
     * @return bool
     */
    public function createGame(GameInterface $game);

    /**
     * @param GameInterface $game
     * @param bool $soft
     * @return bool
     */
    public function deleteGame(GameInterface $game, $soft = true);
}
