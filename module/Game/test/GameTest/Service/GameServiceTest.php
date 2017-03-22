<?php

namespace GameTest\Service;

use Application\Exception\NotFoundException;
use Game\Game;
use Game\GameInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Game\Service\GameService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test GameServiceTest
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
     * @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter
     */
    protected $adapter;

    /**
     * @before
     */
    public function setUpGameData()
    {
        $this->gameData = [
            'game_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts',
            'created'     => '2016-02-28 00:00:00',
            'updated'     => '2016-02-28 00:00:00',
            'deleted'     => null,
            'meta'        => ['desktop' => false, 'unity' => false],
            'flags'       => 7,
            'uris'        => [
                'image_url'  => 'http://bit.ly/XWISPd',
                'banner_url' =>
                    'https://s-media-cache-ak0.pinimcom/736x/0c/bc/25/0cbc259b8805bf2bae57a2909f2a5ba8.jpg',
            ],
            'sort_order'  => 2,
        ];
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->gameService = new GameService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateway()
    {
        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('games')->byDefault();
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
    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        // make sure it is not calling the table gateway select
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->gameService->fetchAll();

        $expectedSelect = new Select(['g' => 'games']);
        $expectedSelect->where(new Where());
        $expectedSelect->order(['sort_order', 'title']);

        $this->assertEquals(
            new DbSelect($expectedSelect, $this->adapter, new HydratingResultSet(new ArraySerializable(), new Game())),
            $result,
            GameService::class . ' did not return a paginator adapter on fetch all'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchGameByIdWithNoPrototype()
    {
        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($actual) {
                $where = new PredicateSet();
                $where->addPredicates(['game_id' => 'sea-turtle']);

                $this->assertEquals(
                    $where,
                    $actual,
                    GameService::class . ' did not build the correct where for fetchGameById'
                );

                $resultSet = new ResultSet();
                $resultSet->initialize([$this->gameData]);

                return $resultSet;
            })->once();

        $this->assertInstanceOf(
            Game::class,
            $this->gameService->fetchGame($this->gameData['game_id']),
            GameService::class . ' did not return a game when no prototype is set'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchGameByIdWithPrototype()
    {
        /** @var \Mockery\MockInterface|GameInterface $prototype */
        $prototype = \Mockery::mock(GameInterface::class);

        $prototype->shouldReceive('exchangeArray')
            ->once();

        $this->tableGateway->shouldReceive('select')
            ->andReturnUsing(function ($actual) {
                $where = new PredicateSet();
                $where->addPredicates(['game_id' => 'sea-turtle']);

                $this->assertEquals(
                    $where,
                    $actual,
                    GameService::class . ' did not build the correct where for fetchGameById'
                );

                $resultSet = new ResultSet();
                $resultSet->initialize([$this->gameData]);

                return $resultSet;
            })->once();

        $this->assertEquals(
            $prototype,
            $this->gameService->fetchGame($this->gameData['game_id'], $prototype),
            GameService::class . ' did not return a game when no prototype is set'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenGameIsNotFound()
    {
        $this->expectException(NotFoundException::class);
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
        $game = new Game($this->gameData);
        $this->tableGateway->shouldReceive('insert')
            ->withArgs(function ($actualData) {
                $expectedData          = $this->gameData;
                $expectedData['meta']  = Json::encode($this->gameData['meta']);
                $expectedData['flags'] = 7;
                $expectedData['uris']  = Json::encode($this->gameData['uris']);

                // remove created date since that is really hard to compare
                $this->assertTrue(
                    is_string($actualData['created']),
                    GameService::class . ' is not transforming the created date to a string'
                );

                $this->assertNotEquals(
                    $actualData['created'],
                    $expectedData['created'],
                    GameService::class . ' did not change the created date on update'
                );

                // remove updated date since that is really hard to compare
                $this->assertTrue(
                    is_string($actualData['updated']),
                    GameService::class . ' is not transforming the updated date to a string'
                );

                $this->assertNotEquals(
                    $actualData['updated'],
                    $expectedData['updated'],
                    GameService::class . ' did not change the updated date on update'
                );

                unset(
                    $expectedData['created'],
                    $actualData['created'],
                    $expectedData['updated'],
                    $actualData['updated']
                );

                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    GameService::class . ' is not going to update the game data correctly'
                );

                return true;
            })
            ->once();

        $this->assertTrue(
            $this->gameService->createGame($game),
            GameService::class . ' did not return true when creating a new game'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateGame()
    {
        $this->tableGateway->shouldReceive('update')
            ->withArgs(function ($actualData, $actualWhere) {
                $this->assertEquals(
                    ['game_id' => $this->gameData['game_id']],
                    $actualWhere,
                    GameService::class . ' is not going to update a game correctly'
                );

                $expectedData          = $this->gameData;
                $expectedData['meta']  = Json::encode($this->gameData['meta']);
                $expectedData['flags'] = 7;
                $expectedData['uris']  = Json::encode($this->gameData['uris']);

                // remove updated date since that is really hard to compare
                $this->assertTrue(
                    is_string($actualData['updated']),
                    GameService::class . ' is not transforming the updated date to a string'
                );

                $this->assertNotEquals(
                    $actualData['updated'],
                    $expectedData['updated'],
                    GameService::class . ' did not change the updated date on update'
                );

                unset($expectedData['updated']);
                unset($actualData['updated']);
                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    GameService::class . ' is not going to update the game data correctly'
                );

                return true;
            })
            ->once();

        $this->assertTrue(
            $this->gameService->saveGame(new Game($this->gameData)),
            GameService::class . ' did not return true on successful update of a game'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateDeletedGame()
    {
        $this->tableGateway->shouldReceive('update')
            ->withArgs(function ($actualData, $actualWhere) {
                $this->assertEquals(
                    ['game_id' => $this->gameData['game_id']],
                    $actualWhere,
                    GameService::class . ' is not going to update a game correctly'
                );

                $expectedData          = $this->gameData;
                $expectedData['meta']  = Json::encode($this->gameData['meta']);
                $expectedData['flags'] = 7;
                $expectedData['uris']  = Json::encode($this->gameData['uris']);

                // remove updated date since that is really hard to compare
                $this->assertTrue(
                    is_string($actualData['updated']),
                    GameService::class . ' is not transforming the updated date to a string'
                );

                $this->assertNotEquals(
                    $actualData['updated'],
                    $expectedData['updated'],
                    GameService::class . ' did not change the updated date on update'
                );


                $this->assertNull(
                    $expectedData['deleted'],
                    GameService::class . ' did not remove the deleted date on the game'
                );

                unset($expectedData['updated']);
                unset($actualData['updated']);
                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    GameService::class . ' is not going to update the game data correctly'
                );

                return true;
            })
            ->once();

        $game = new Game($this->gameData);
        $game->setDeleted(new \DateTime('now'));
        $this->assertTrue(
            $this->gameService->saveGame($game, true),
            GameService::class . ' did not return true on successful update of a game'
        );

        $this->assertNull(
            $game->getDeleted(),
            GameService::class . ' did not remove the deleted date on the game'
        );
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteGame()
    {
        $this->tableGateway->shouldReceive('select')->never();
        $this->tableGateway->shouldReceive('update')
            ->withArgs(function ($actualData, $actualWhere) {
                $this->assertEquals(
                    ['game_id' => $this->gameData['game_id']],
                    $actualWhere,
                    GameService::class . ' is not going to update a game correctly'
                );

                $expectedData          = $this->gameData;
                $expectedData['meta']  = Json::encode($this->gameData['meta']);
                $expectedData['flags'] = 7;
                $expectedData['uris']  = Json::encode($this->gameData['uris']);

                // remove updated date since that is really hard to compare
                $this->assertTrue(
                    is_string($actualData['deleted']),
                    GameService::class . ' is not transforming the updated date to a string'
                );

                $this->assertNotEmpty(
                    $actualData['deleted'],
                    GameService::class . ' did not change the updated date on update'
                );

                return true;
            })->once();
        $this->tableGateway->shouldReceive('delete')->never();

        $this->assertTrue(
            $this->gameService->deleteGame(new Game($this->gameData)),
            GameService::class . ' did not return true when soft deleting a game'
        );
    }

    /**
     * @test
     */
    public function testItShouldHardDeleteGame()
    {
        $this->tableGateway->shouldReceive('update')->never();
        $this->tableGateway->shouldReceive('delete')->with(['game_id' => 'sea-turtle'])->once();
        $this->assertTrue(
            $this->gameService->deleteGame(new Game($this->gameData), false),
            GameService::class . ' did not return true when hard deleting a game'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllGamesUsingFlagKeys()
    {
        $where = new PredicateSet();

        // Order matters for this test
        $where->orPredicate(new Expression('flags & ? = ?', 4, 4));
        $where->orPredicate(new Expression('flags & ? = ?', 2, 2));
        $where->orPredicate(new Expression('flags & ? = ?', 1, 1));

        $expectedSelect = new Select(['g' => 'games']);
        $expectedSelect->where($where);
        $expectedSelect->order(['sort_order', 'title']);

        $result = $this->gameService->fetchAll(
            ['coming_soon' => true, 'featured' => true, 'global' => true]
        );

        $this->assertEquals(
            new DbSelect($expectedSelect, $this->adapter, new HydratingResultSet(new ArraySerializable(), new Game())),
            $result,
            GameService::class . ' did not return a paginator adapter on fetch all with flag keys'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllGamesUsingMixedFlagKeys()
    {
        $where = new PredicateSet();

        // Order matters for this test
        $where->orPredicate(new Expression('flags & ? != ?', 4, 4));
        $where->orPredicate(new Expression('flags & ? = ?', 2, 2));
        $where->orPredicate(new Expression('flags & ? = ?', 1, 1));

        $expectedSelect = new Select(['g' => 'games']);
        $expectedSelect->where($where);
        $expectedSelect->order(['sort_order', 'title']);

        $result = $this->gameService->fetchAll(['coming_soon' => false, 'featured' => true, 'global' => true]);

        $this->assertEquals(
            new DbSelect($expectedSelect, $this->adapter, new HydratingResultSet(new ArraySerializable(), new Game())),
            $result,
            GameService::class . ' did not return a paginator adapter on fetch all with mixed flag keys'
        );
    }
}
