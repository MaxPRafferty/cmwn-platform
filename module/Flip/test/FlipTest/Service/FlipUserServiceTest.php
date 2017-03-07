<?php

namespace FlipTest\Service;

use Application\Exception\NotFoundException;
use Flip\EarnedFlip;
use Flip\Service\FlipUserService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Ramsey\Uuid\Uuid;
use User\Adult;
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
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test FlipUserServiceTest
 *
 * @group Flip
 * @group User
 * @group Service
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipUserServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var FlipUserService(
     */
    protected $flipService;

    /**
     * @var \Mockery\MockInterface|TableGateway
     */
    protected $tableGateway;

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
     * @test
     */
    public function testItShouldFetchAllFlipsForUserWithDefaultValuesAndStringForUserId()
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
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actualInsert) {
                $this->assertEquals(
                    ['user_id', 'flip_id', 'earned', 'acknowledge_id'],
                    array_keys($actualInsert),
                    FlipUserService::class . ' is setting the wrong fields'
                );

                $this->assertArrayHasKey(
                    'earned',
                    $actualInsert,
                    FlipUserService::class . ' did not include the earned date on attach'
                );

                $this->assertArrayHasKey(
                    'acknowledge_id',
                    $actualInsert,
                    FlipUserService::class . ' did not include the acknowledge id on attach'
                );

                $this->assertEquals(
                    'foo-bar',
                    $actualInsert['user_id'],
                    FlipUserService::class . ' service did not use correct user Id'
                );

                $this->assertEquals(
                    'baz-bat',
                    $actualInsert['flip_id'],
                    FlipUserService::class . ' did not use correct flip Id'
                );

                $this->assertNotInstanceOf(
                    \DateTime::class,
                    $actualInsert['earned'],
                    FlipUserService::class . ' did not set the earned date correctly'
                );

                return true;
            });

        $this->flipService->attachFlipToUser('foo-bar', 'baz-bat');
    }

    /**
     * @test
     */
    public function testItShouldAcknowledgeFlip()
    {
        $ackId      = Uuid::uuid1();
        $earnedFlip = new EarnedFlip();
        $earnedFlip->setAcknowledgeId($ackId);
        $this->tableGateway->shouldReceive('update')
            ->with(['acknowledge_id' => null], ['acknowledge_id' => $ackId])
            ->andReturn(1)
            ->once();

        $this->assertTrue(
            $this->flipService->acknowledgeFlip($earnedFlip),
            FlipUserService::class . ' did not acknowledge earned flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotAcknowledgeFlip()
    {
        $ackId      = Uuid::uuid1();
        $earnedFlip = new EarnedFlip();
        $earnedFlip->setAcknowledgeId($ackId);
        $this->tableGateway->shouldReceive('update')
            ->with(['acknowledge_id' => null], ['acknowledge_id' => $ackId])
            ->andReturn(0)
            ->once();

        $this->assertFalse(
            $this->flipService->acknowledgeFlip($earnedFlip),
            FlipUserService::class . ' did acknowledged earned flip'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchTheLatestAcknowledgeFlip()
    {
        $earnedFlip = new EarnedFlip();
        $earnedFlip->setFlipId('played-farmville-manchuck-edition');
        $earnedFlip->setTitle('Be the best farmer');
        $earnedFlip->setDescription('Manchuck is the best farmer in the world');
        $earnedFlip->setAcknowledgeId('foobar-bazbat');

        $expectedResultSet = new HydratingResultSet(new ArraySerializable(), new \ArrayObject());
        $expectedResultSet->initialize([$earnedFlip->getArrayCopy()]);
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
            ->andReturnUsing(function ($actualSelect) use (&$expectedSelect, &$expectedResultSet) {
                $this->assertEquals(
                    $expectedSelect,
                    $actualSelect,
                    FlipUserService::class . ' is selecting the wrong stuff for acknowledge flip'
                );

                return $expectedResultSet;
            });

        $this->assertEquals(
            $earnedFlip,
            $this->flipService->fetchLatestAcknowledgeFlip(new Adult(['user_id' => 'manchuck'])),
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
        $this->tableGateway->shouldReceive('selectWith')->andReturn($expectedResultSet);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('No flips to acknowledge');
        $this->flipService->fetchLatestAcknowledgeFlip(new Adult(['user_id' => 'manchuck']));
    }
}
