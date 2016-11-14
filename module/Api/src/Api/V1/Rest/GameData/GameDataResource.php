<?php

namespace Api\V1\Rest\GameData;

use Game\Service\SaveGameServiceInterface;
use Zend\Db\Sql\Predicate\Operator;
use ZF\Rest\AbstractResourceListener;

/**
 * Class GameDataResource
 * @package Api\V1\Rest\GameData
 */
class GameDataResource extends AbstractResourceListener
{

    /**
     * @var SaveGameServiceInterface
     */
    protected $saveGameService;

    /**
     * GameDataResource constructor.
     * @param SaveGameServiceInterface $saveGameService
     */
    public function __construct($saveGameService)
    {
        $this->saveGameService = $saveGameService;
    }

    /**
     * @param mixed $gameId
     * @return GameDataCollection
     */
    public function fetch($gameId)
    {
        $where = $gameId === null ? null : new Operator('sg.game_id', Operator::OP_EQ, $gameId);
        $gameData = $this->saveGameService->fetchAllSaveGameData($where, new GameDataEntity());
        return new GameDataCollection($gameData);
    }

    /**
     * @param array $params
     * @return GameDataCollection
     */
    public function fetchAll($params = [])
    {
        $gameData = $this->saveGameService->fetchAllSaveGameData(null, new GameDataEntity());
        return new GameDataCollection($gameData);
    }
}
