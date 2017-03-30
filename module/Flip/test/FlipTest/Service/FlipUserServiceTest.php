<?php

namespace FlipTest\Service;

use Application\Exception\NotFoundException;
use Flip\EarnedFlip;
use Flip\EarnedFlipInterface;
use Flip\Service\FlipUserService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Ramsey\Uuid\Uuid;
use User\Adult;
use User\PlaceHolder;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\IsNotNull;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Tests the flip user service
 */
class FlipUserServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FlipUserService
     */
    protected $flipService;

    /**
     * @var \Mockery\MockInterface|TableGateway
     */
    protected $tableGateway;

    /**
     * @var EarnedFlip
     */
    protected $earnedFlip;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->flipService = new FlipUserService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|Adapter $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_flips')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpDefaultTestEarnedFlip()
    {
        $this->earnedFlip = new EarnedFlip([
            'flip_id'        => 'foo-bar',
            'title'          => 'Manchuck Flip',
            'description'    => 'The Best Flip to earn',
            'earned_by'      => 'manchuck',
            'earned'         => new \DateTime('1982-05-13 23:43:00'),
            'acknowledge_id' => Uuid::uuid1(),
            'urls'           => [
                EarnedFlip::IMAGE_COIN     => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                EarnedFlip::IMAGE_UNEARNED => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                EarnedFlip::IMAGE_EARNED   => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                EarnedFlip::IMAGE_STATIC   => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
                EarnedFlip::IMAGE_DEFAULT  => 'https://media.changemyworldnow.com/f/34897dva89s7490890.png',
            ],
        ]);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlipsForUser()
    {
        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new EarnedFlip());
        $where             = new Where();
        $expectedSelect    = new Select(['uf' => 'user_flips']);

        $expectedSelect->columns([
            'earned_by' => 'user_id',
            'earned',
            'acknowledge_id',
        ]);

        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $expectedSelect->join(
            ['f' => 'flips'],
            new Expression('uf.user_id = ?', 'foo-bar'),
            '*',
            Select::JOIN_LEFT
        );
        $expectedSelect->where($where);
        $expectedSelect->group('f.flip_id');
        $expectedSelect->order(['uf.earned', 'f.title']);

        $expectedAdapter = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchEarnedFlipsForUser('foo-bar'),
            FlipUserService::class . ' did not return correct paginator adapter using a string for the user id'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlipsForUserWithCustomProtoTypeAndWhere()
    {
        /** @var \Mockery\MockInterface|\Flip\EarnedFlipInterface $prototype */
        $prototype         = \Mockery::mock('\Flip\EarnedFlipInterface');
        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $where             = new Where();
        $expectedSelect    = new Select(['uf' => 'user_flips']);

        $expectedSelect->columns([
            'earned_by' => 'user_id',
            'earned',
            'acknowledge_id',
        ]);
        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $expectedSelect->join(
            ['f' => 'flips'],
            new Expression('uf.user_id = ?', 'foo-bar'),
            '*',
            Select::JOIN_LEFT
        );
        $expectedSelect->group('f.flip_id');
        $expectedSelect->order(['uf.earned', 'f.title']);

        $where->addPredicate(new Operator('foo', '=', 'bar'));
        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $expectedSelect->where($where);

        $expectedAdapter = new DbSelect(
            $expectedSelect,
            $this->tableGateway->getAdapter(),
            $expectedResultSet
        );

        $this->assertEquals(
            $expectedAdapter,
            $this->flipService->fetchEarnedFlipsForUser('foo-bar', $where, $prototype),
            FlipUserService::class . ' did not return correct paginator adapter using a custom prototype'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachFlipToUser()
    {
        $user = new PlaceHolder();
        $user->setUserId('foo-bar');
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->withArgs(function ($actualData) {
                $expectedData = [
                    'user_id' => 'foo-bar',
                    'flip_id' => 'fizz-buzz',
                ];

                $this->assertNotEmpty(
                    $actualData['earned'],
                    FlipUserService::class . ' is not setting the correct earned date when attaching a flip'
                );

                $this->assertNotEmpty(
                    $actualData['acknowledge_id'],
                    FlipUserService::class . ' is not setting the correct acknowledge id when attaching a flip'
                );

                unset($actualData['acknowledge_id'], $actualData['earned']);

                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    FlipUserService::class . ' is not attaching flips correctly'
                );

                return true;
            })
            ->andReturn(1);

        $this->assertTrue(
            $this->flipService->attachFlipToUser($user, 'fizz-buzz'),
            FlipUserService::class . ' did not return true when attaching a flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldAcknowledgeFlip()
    {
        $this->tableGateway->shouldReceive('update')
            ->once()
            ->withArgs(function ($actualData, $actualWhere) {
                $this->assertEquals(
                    ['acknowledge_id' => null],
                    $actualData,
                    FlipUserService::class . ' is not setting the flip to null on acknowledgement'
                );

                $this->assertEquals(
                    ['acknowledge_id' => $this->earnedFlip->getAcknowledgeId()],
                    $actualWhere,
                    FlipUserService::class . ' is not acknowledging the correct flip'
                );

                return true;
            })
            ->andReturn(1);

        $this->assertTrue(
            $this->flipService->acknowledgeFlip($this->earnedFlip),
            FlipUserService::class . ' did not acknowledge earned flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchTheLatestAcknowledgeFlip()
    {
        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new \ArrayObject());
        $expectedResultSet->initialize([
            [
                'flip_id'        => $this->earnedFlip->getFlipId(),
                'title'          => $this->earnedFlip->getTitle(),
                'description'    => $this->earnedFlip->getDescription(),
                'earned_by'      => $this->earnedFlip->getEarnedBy(),
                'earned'         => $this->earnedFlip->getEarned()->format('Y-m-d H:i:s'),
                'acknowledge_id' => $this->earnedFlip->getAcknowledgeId(),
                'urls'           => Json::encode($this->earnedFlip->getUris()),
            ],
        ]);

        $where          = new Where();
        $expectedSelect = new Select(['uf' => 'user_flips']);

        $expectedSelect->columns([
            'earned_by' => 'user_id',
            'earned',
            'acknowledge_id',
        ]);
        $expectedSelect->join(
            ['f' => 'flips'],
            new Expression('uf.user_id = ?', 'manchuck'),
            '*',
            Select::JOIN_LEFT
        );
        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $where->addPredicate(new IsNotNull('uf.acknowledge_id'));

        $expectedSelect->where($where);
        $expectedSelect->order(['uf.earned DESC']);
        $expectedSelect->limit(1);

        $this->tableGateway->shouldReceive('selectWith')
            ->once()
            ->andReturnUsing(function ($actualSelect) use (&$expectedSelect, &$expectedResultSet) {
                $this->assertEquals(
                    $expectedSelect,
                    $actualSelect,
                    FlipUserService::class . ' is selecting the wrong stuff for acknowledge flip'
                );

                return $expectedResultSet;
            });

        $this->assertEquals(
            $this->earnedFlip,
            $this->flipService->fetchLatestAcknowledgeFlip(new PlaceHolder(['user_id' => 'manchuck'])),
            FlipUserService::class . ' did not return the earned flip for the best farmer in the world'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchTheLatestAcknowledgeFlipWithPrototype()
    {
        /** @var \Mockery\MockInterface|EarnedFlipInterface $prototype */
        $prototype = \Mockery::mock(EarnedFlipInterface::class);

        $flipResult =[
            'flip_id'        => $this->earnedFlip->getFlipId(),
            'title'          => $this->earnedFlip->getTitle(),
            'description'    => $this->earnedFlip->getDescription(),
            'earned_by'      => $this->earnedFlip->getEarnedBy(),
            'earned'         => $this->earnedFlip->getEarned()->format('Y-m-d H:i:s'),
            'acknowledge_id' => $this->earnedFlip->getAcknowledgeId(),
            'urls'           => Json::encode($this->earnedFlip->getUris()),
        ];

        $prototype->shouldReceive('exchangeArray')
            ->once()
            ->with($flipResult);

        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new \ArrayObject());
        $expectedResultSet->initialize([$flipResult]);

        $where          = new Where();
        $expectedSelect = new Select(['uf' => 'user_flips']);

        $expectedSelect->columns([
            'earned_by' => 'user_id',
            'earned',
            'acknowledge_id',
        ]);
        $expectedSelect->join(
            ['f' => 'flips'],
            new Expression('uf.user_id = ?', 'manchuck'),
            '*',
            Select::JOIN_LEFT
        );
        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $where->addPredicate(new IsNotNull('uf.acknowledge_id'));

        $expectedSelect->where($where);
        $expectedSelect->order(['uf.earned DESC']);
        $expectedSelect->limit(1);

        $this->tableGateway->shouldReceive('selectWith')
            ->once()
            ->andReturnUsing(function ($actualSelect) use (&$expectedSelect, &$expectedResultSet) {
                $this->assertEquals(
                    $expectedSelect,
                    $actualSelect,
                    FlipUserService::class . ' is selecting the wrong stuff for acknowledge flip'
                );

                return $expectedResultSet;
            });

        $this->assertSame(
            $prototype,
            $this->flipService->fetchLatestAcknowledgeFlip(
                new PlaceHolder(['user_id' => 'manchuck']),
                $prototype
            ),
            FlipUserService::class . ' did not return the earned flip for the best farmer in the world'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenThereAreNoFlipsToAcknowledge()
    {
        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new \ArrayObject());
        $expectedResultSet->initialize([]);
        $this->tableGateway->shouldReceive('selectWith')
            ->once()
            ->andReturn($expectedResultSet);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('No flips to acknowledge');
        $this->flipService->fetchLatestAcknowledgeFlip(new PlaceHolder(['user_id' => 'manchuck']));
    }
}
