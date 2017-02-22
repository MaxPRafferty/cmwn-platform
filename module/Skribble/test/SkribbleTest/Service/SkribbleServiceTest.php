<?php

namespace SkribbleTest\Service;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Ramsey\Uuid\Uuid;
use Skribble\Service\SkribbleService;
use Skribble\Skribble;
use User\Child;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Test SkribbleServiceTest
 *
 * @group Skribble
 * @group SkribbleService
 * @group Service
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SkribbleServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SkribbleService
     */
    protected $skribbleService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var Skribble
     */
    protected $skribble;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->skribbleService = new SkribbleService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('skribbles')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpSkribble()
    {
        $created = new Child(['user_id' => 'baz-bat']);
        $friend  = new Child(['user_id' => 'fizz-buzz']);

        $this->skribble = new Skribble(['skribble_id' => 'foo-bar']);
        $this->skribble->setCreatedBy($created);
        $this->skribble->setFriendTo($friend);
    }

    /**
     * @test
     */
    public function testItShouldFetchSkribbleById()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->once()
            ->andReturnUsing(function ($actual) {
                $this->assertEquals(
                    ['skribble_id' => 'foo-bar'],
                    $actual,
                    'Skribble Service is not fetching Skribblies correctly'
                );

                $result = new ResultSet();
                $result->initialize([$this->skribble->getArrayCopy()]);

                return $result;
            });

        $skribble = $this->skribbleService->fetchSkribble('foo-bar');
        $this->assertEquals($this->skribble, $skribble, 'Skribble was not hydrated from the DB correctly');
    }

    /**
     * @test
     */
    public function testItShouldFetchSkribbleAndHydrateObject()
    {
        /** @var \Mockery\MockInterface|\Skribble\SkribbleInterface $prototype */
        $prototype = \Mockery::mock('\Skribble\SkribbleInterface');

        $prototype->shouldReceive('exchangeArray')
            ->with($this->skribble->getArrayCopy())
            ->once();

        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($actual) {
                $this->assertEquals(
                    ['skribble_id' => 'foo-bar'],
                    $actual,
                    'Skribble Service is not fetching Skribblies correctly'
                );

                $result = new ResultSet();
                $result->initialize([$this->skribble->getArrayCopy()]);

                return $result;
            });

        $this->skribbleService->fetchSkribble('foo-bar', $prototype);
    }

    /**
     * @test
     */
    public function testItShouldCreateSkribbleCorrectly()
    {
        /** @var \Mockery\MockInterface|\Skribble\SkribbleInterface $prototype */
        $prototype = \Mockery::mock('\Skribble\SkribbleInterface');

        $prototype->shouldReceive('getArrayCopy')
            ->with($this->skribble->getArrayCopy())
            ->atLeast(1);

        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actual) {
                $this->assertNotNull(
                    $this->skribble->getCreated(),
                    'Skirbble Service did not set created date on create'
                );

                $this->assertNotNull(
                    $this->skribble->getUpdated(),
                    'Skirbble Service did not set update date on create'
                );

                $this->assertNotEmpty(
                    $this->skribble->getSkribbleId(),
                    'Skirbble Service did not set skribble id on create'
                );

                $this->assertTrue(is_array($actual));

                $expected            = $this->skribble->getArrayCopy();
                $expected['rules']   = '{"background":null,"effect":null,"sound":null,"items":[],"messages":[]}';
                $expected['created'] = $this->skribble->getCreated()->format("Y-m-d H:i:s");
                $expected['updated'] = $this->skribble->getUpdated()->format("Y-m-d H:i:s");
                unset($expected['deleted']);

                $this->assertArrayNotHasKey('deleted', $actual, 'Skribble service must not set deleted on create');
                $this->assertEquals(
                    $expected,
                    $actual,
                    'Skribble Service is saving something strange'
                );

                return 1;
            });

        $this->assertTrue(
            $this->skribbleService->createSkribble($this->skribble),
            'Skribble service did not return true on create'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateSkribble()
    {
        $originalUpdate = new \DateTime('-1 Day');
        $this->skribble->setSkribbleId(Uuid::uuid1());
        $this->skribble->setUpdated($originalUpdate);
        $result = new ResultSet();
        $result->initialize([$this->skribble->getArrayCopy()]);

        $this->tableGateway
            ->shouldReceive('select')
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->once()
            ->andReturnUsing(function ($actualData, $where) use (&$originalUpdate) {
                $this->assertEquals(
                    ['skribble_id' => $this->skribble->getSkribbleId()],
                    $where,
                    'Skribble service is going to update the wrong skribble'
                );

                $this->assertNotEquals(
                    $originalUpdate->format('Y-m-d H:i:s'),
                    $actualData['updated'],
                    'Skribble Service did not update the updated data'
                );

                $expectedData = $this->skribble->getArrayCopy();
                unset($expectedData['created']);

                $expectedData['rules']   = '{"background":null,"effect":null,"sound":null,"items":[],"messages":[]}';
                $expectedData['updated'] = $this->skribble->getUpdated()->format("Y-m-d H:i:s");

                $this->assertEquals(
                    $expectedData,
                    $actualData,
                    'Skribble service is updating the wrong data'
                );

                return 1;
            });

        $this->assertTrue(
            $this->skribbleService->updateSkribble($this->skribble),
            'Skribble Service did not report back true on update'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSkribblesForUser()
    {
        $where = new Where();
        $where->andPredicate(new Expression('(created_by = ? OR friend_to = ?)', 'baz-bat', 'baz-bat'));
        $select = new Select(['s' => 'skribbles']);
        $select->where($where);
        $select->order(['s.updated DESC']);

        $expected = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet = new HydratingResultSet(new ArraySerializable(), new Skribble())
        );

        $this->assertEquals(
            $expected,
            $this->skribbleService->fetchAllForUser('baz-bat'),
            'Skribble will return improper result for fetching all'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllReceivedSkribblesForUser()
    {
        $where = new Where();
        $where->addPredicate(new Operator('friend_to', '=', 'baz-bat'));
        $where->addPredicate(new Operator('status', '=', 'COMPLETE'));
        $select = new Select(['s' => 'skribbles']);
        $select->where($where);
        $select->order(['s.updated DESC']);

        $expected = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet = new HydratingResultSet(new ArraySerializable(), new Skribble())
        );

        $this->assertEquals(
            $expected,
            $this->skribbleService->fetchReceivedForUser('baz-bat'),
            'Skribble will return improper result for fetching all received'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSentSkribblesForUser()
    {
        $where  = new Where();
        $status = new PredicateSet();
        $status->orPredicate(new Operator('status', '=', 'COMPLETE'));
        $status->orPredicate(new Operator('status', '=', 'PROCESSING'));

        $where->addPredicate(new Operator('created_by', '=', 'baz-bat'));
        $where->addPredicate($status);

        $select = new Select(['s' => 'skribbles']);
        $select->where($where);
        $select->order(['s.updated DESC']);

        $expected = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet = new HydratingResultSet(new ArraySerializable(), new Skribble())
        );

        $this->assertEquals(
            $expected,
            $this->skribbleService->fetchSentForUser('baz-bat'),
            'Skribble will return improper result for fetching all sent'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllDraftSkribblesForUser()
    {
        $where = new Where();
        $where->addPredicate(new Operator('status', '=', 'NOT_COMPLETE'));
        $where->addPredicate(new Operator('created_by', '=', 'baz-bat'));
        $select = new Select(['s' => 'skribbles']);
        $select->where($where);
        $select->order(['s.updated DESC']);

        $expected = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet = new HydratingResultSet(new ArraySerializable(), new Skribble())
        );

        $this->assertEquals(
            $expected,
            $this->skribbleService->fetchDraftForUser('baz-bat'),
            'Skribble will return improper result for fetching all drafts'
        );
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteSkribble()
    {
        $this->skribble->setSkribbleId(Uuid::uuid1());
        $result = new ResultSet();
        $result->initialize([$this->skribble->getArrayCopy()]);

        $this->tableGateway
            ->shouldReceive('select')
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->once()
            ->andReturnUsing(function ($actualData, $where) {
                $this->assertNotNull(
                    $this->skribble->getDeleted(),
                    'Skribble Service is not setting delete date on skribble'
                );

                $this->assertEquals(
                    ['deleted' => $this->skribble->getDeleted()->format('Y-m-d H:i:s')],
                    $actualData,
                    'Skribble service will not set the deleted date in the DB '
                );

                $this->assertEquals(
                    ['skribble_id' => $this->skribble->getSkribbleId()],
                    $where,
                    'Skribble service will be soft deleting the wrong skribble'
                );

                return 1;
            });

        $this->assertTrue(
            $this->skribbleService->deleteSkribble($this->skribble),
            'Skribble Service did not return true when soft deleting skribble'
        );
    }
}
