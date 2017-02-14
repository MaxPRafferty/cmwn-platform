<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Game\GameInterface;
use User\UserInterface;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Specification for UserGameService
 */
interface UserGameServiceInterface
{
    /**
     * @param UserInterface $user
     * @param Where | array | null $where
     * @param GameInterface | null $prototype
     * @return AdapterInterface
     */
    public function fetchAllGamesForUser(
        UserInterface $user,
        $where = null,
        GameInterface $prototype = null
    ) : AdapterInterface;

    /**
     * @param UserInterface $user
     * @param GameInterface $gameId
     * @param GameInterface | null $prototype
     * @return GameInterface
     * @throws NotFoundException
     */
    public function fetchGameForUser(
        UserInterface $user,
        GameInterface $game,
        GameInterface $prototype = null
    ) : GameInterface;

    /**
     * @param UserInterface $user
     * @param GameInterface $game
     * @return bool
     */
    public function attachGameToUser(UserInterface $user, GameInterface $game) : bool;

    /**
     * @param UserInterface $user
     * @param GameInterface $game
     * @return bool
     */
    public function detachGameForUser(UserInterface $user, GameInterface $game) : bool;
}
