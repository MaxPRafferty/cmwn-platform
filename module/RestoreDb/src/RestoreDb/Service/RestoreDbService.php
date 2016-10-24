<?php

namespace RestoreDb\Service;

use RestoreDb\RestoreException;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class RestoreDbService
 * @package RestoreDb
 */
class RestoreDbService implements RestoreDbServiceInterface
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var array $dataSet
     */
    protected $dataSet;

    /**
     * RestoreDbService constructor.
     * @param array $dataSet
     * @param AdapterInterface $adapter
     */
    public function __construct($dataSet, AdapterInterface $adapter)
    {
        $this->dataSet = $dataSet;
        $this->adapter = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function runDbStateRestorer()
    {
        if (!isset($this->dataSet['users'])) {
            return false;
        }

        foreach ($this->dataSet['users'] as $user) {
            if ($this->resetUser($user)) {
                try {
                    $userId = $user['user_id'];
                    $this->resetDataForUser($userId, 'user_flips');
                    $this->resetDataForUser($userId, 'user_saves');
                    $this->resetDataForUser($userId, 'user_friends');
                    $this->resetDataForUser($userId, 'user_images');
                    $this->resetDataForUser($userId, 'user_suggestions');
                } catch (\PDOException $e) {
                    throw new RestoreException('Exception while restoring state for ' . $user['user_id']);
                }
            }
        }

        return true;
    }

    /**
     * @param $user
     * @return bool
     */
    protected function resetUser($user)
    {
        $userId = $user['user_id'];
        $userTableGateWay = $this->tableGateWay('users');
        $rowSet = $userTableGateWay->select(['user_id' => $userId]);

        if (!$rowSet->current()) {
            return false;
        }

        unset($user['user_id']);

        $userTableGateWay->update($user, ['user_id' => $userId]);
        return true;
    }

    /**
     * @param $userId
     * @param $tableName
     */
    protected function resetDataForUser($userId, $tableName)
    {
        $tableGateWay = $this->tableGateWay($tableName);

        $tableGateWay->delete(['user_id' => $userId]);

        if (!isset($this->dataSet[$tableName])) {
            return;
        }

        foreach ($this->dataSet[$tableName] as $tableEntry) {
            if (!isset($tableEntry['user_id']) || $tableEntry['user_id'] !== $userId) {
                continue;
            }

            $tableGateWay->insert($tableEntry);
        }
    }

    /**
     * @param $tableName
     * @return TableGateway
     */
    protected function tableGateWay($tableName)
    {
        return new TableGateway($tableName, $this->adapter);
    }
}
