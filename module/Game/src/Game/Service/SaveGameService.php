<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Game\GameInterface;
use Game\SaveGame;
use Game\SaveGameInterface;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\TableGateway\TableGateway;
use Zend\Json\Json;

/**
 * Class SaveGameService
 */
class SaveGameService implements SaveGameServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->tableGateway = $gateway;
    }

    /**
     * @param SaveGameInterface $gameData
     *
     * @return bool
     */
    public function saveGame(SaveGameInterface $gameData)
    {
        $gameData->setCreated(new \DateTime());
        $data         = $gameData->getArrayCopy();
        $data['data'] = !is_string($data['data'])
            ? Json::encode($data['data'])
            : $data['data'];
        
        $data['created'] = $gameData->getCreated()->format("Y-m-d H:i:s");
        try {
            $this->fetchSaveGameForUser($gameData->getUserId(), $gameData->getGameId());
            $this->deleteSaveForUser($gameData->getUserId(), $gameData->getGameId());
        } catch (NotFoundException $notFound) {
            // Nothing to do here move along
        }

        $this->tableGateway->insert($data);
        return true;
    }

    /**
     * @param $user
     * @param $game
     *
     * @return bool
     */
    public function deleteSaveForUser($user, $game)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $gameId = $game instanceof GameInterface ? $game->getGameId() : $game;

        $this->tableGateway->delete(['user_id' => $userId, 'game_id' => $gameId]);
        return true;
    }

    /**
     * @param $user
     * @param $game
     * @param null $prototype
     * @param null $where
     *
     * @return SaveGame|SaveGameInterface
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchSaveGameForUser($user, $game, $prototype = null, $where = null)
    {
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

        $prototype = $prototype instanceof SaveGameInterface ? $prototype : new SaveGame();
        $prototype->exchangeArray((array) $row);

        return $prototype;
    }
}
