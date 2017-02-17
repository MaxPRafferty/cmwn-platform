<?php

namespace GameTest\Service;

use Game\Game;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Game\Service\GameService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Where;

/**
 * Test GameServiceTest
 *
 * @group Game
 * @group Service
 * @group GameService
 */
class GameServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var GameService
     */
    protected $gameService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var array
     */
    protected $gameData;

    /**
     * @before
     */
    public function setUpGameData()
    {
        $this->gameData = [
            "game_id"     => "sea-turtle",
            "title"       => "Sea Turtle",
            "description" => "Sea Turtles are wondrous creatures! Get cool turtle facts",
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => null,
            'meta'        => '{"desktop" : false, "unity" : false}',
        ];
    }

    /**
     * @before
     */
    public function setUpService()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('games')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
        $this->gameService = new GameService($this->tableGateway);
    }

    /**
     * Tests the service returns a pagination adapter by default
     *
     * @test
     */
    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->gameService->fetchAll(null);
        $this->assertInstanceOf('\Zend\Paginator\Adapter\AdapterInterface', $result);
    }

    /**
     * @test
     */
    public function testItShouldFetchGameById()
    {
        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($actual) {
                $where = new Where();
                $where->equalTo('game_id', 'sea-turtle');

                $this->assertEquals($where, $actual);
                $resultSet = new ResultSet();
                $resultSet->initialize([$this->gameData]);

                return $resultSet;
            })->once();

        $this->assertInstanceOf('Game\Game', $this->gameService->fetchGame($this->gameData['game_id']));
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenGameIsNotFound()
    {
        $this->expectException('Application\Exception\NotFoundException');
        $this->expectExceptionMessage('Game not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->gameService->fetchGame('foo');
    }

    /**
     * @test
     */
    public function testItShouldCreateGame()
    {
        $gameData = $this->gameData;
        unset($gameData['game_id']);
        $game = new Game($gameData);

        $this->tableGateway->shouldReceive('insert')->once();

        $this->assertNull($game->getGameId());

        $this->gameService->createGame($game);

        $this->assertEquals('sea-turtle', $game->getGameId());
    }

    /**
     * @test
     */
    public function testItShouldUpdateGame()
    {
        $resultSet = new ResultSet();
        $resultSet->initialize([$this->gameData]);
        $this->tableGateway->shouldReceive('select')->once()->andReturn($resultSet);
        $this->tableGateway->shouldReceive('update')->once();

        $this->assertEmpty($this->gameService->saveGame(new Game($this->gameData)));
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteGame()
    {
        $resultSet = new ResultSet();
        $resultSet->initialize([$this->gameData]);
        $this->tableGateway->shouldReceive('select')->once()->andReturn($resultSet);
        $this->tableGateway->shouldReceive('update')->once();
        $this->assertTrue($this->gameService->deleteGame(new Game($this->gameData)));
    }

    /**
     * @test
     */
    public function testItShouldHardDeleteGame()
    {
        $resultSet = new ResultSet();
        $resultSet->initialize([$this->gameData]);
        $this->tableGateway->shouldReceive('select')->once()->andReturn($resultSet);
        $this->tableGateway->shouldReceive('delete')->once();
        $this->assertTrue($this->gameService->deleteGame(new Game($this->gameData), false));
    }
}
