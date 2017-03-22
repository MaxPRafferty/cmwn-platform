<?php

namespace GameTest\Service;

use Game\Service\UserGameService;
use PHPUnit\Framework\TestCase as TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

/**
 * Test UserGameServiceTest
 */
class UserGameServiceTest extends TestCase
{
    /**
     * @var GameService
     */
    protected $gameService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter
     */
    protected $adapter;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->gameService = new UserGameService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateway()
    {
        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_saves')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($this->adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpAdapter()
    {
        $this->adapter = \Mockery::mock(Adapter::class);
        $this->adapter->shouldReceive('getPlatform')->byDefault();
    }

    /**
     * @test
     */
    public function testItShouldFetchAllGamesForUserCorrectly()
    {
        $where  = new PredicateSet();
        $select = new Select(['ug' => 'user_games']);
        $select->columns([]);
        $select->join(
            ['g' => 'games'],
            'ug.game_id = g.game_id',
            '*',
            Select::JOIN_RIGHT_OUTER
        );

        $where->addPredicate(
            new PredicateSet(
                [
                    new Expression('g.flags & ? = ?', GameInterface::GAME_GLOBAL, GameInterface::GAME_GLOBAL),
                    new Operator('ug.user_id', '=', 'manchuck'),
                ],
                PredicateSet::COMBINED_BY_OR
            )
        );

        $select->where($where);
    }
}
