<?php

namespace GroupTest\Service;

use Application\Exception\NotFoundException;
use Application\Utils\Type\TypeInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Group\Group;
use Group\Service\GroupService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Pdo\Connection;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\Predicate as Where;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\TableGateway\TableGateway;

/**
 * Test GroupServiceTest
 *
 * @group Group
 * @group Service
 * @group GroupService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\Adapter\Driver\Pdo\Connection
     */
    protected $connection;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = new GroupService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateway()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\Driver\DriverInterface $driver */
        $driver = \Mockery::mock(DriverInterface::class);

        $driver->shouldReceive('getConnection')->andReturn($this->connection)->byDefault();

        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();
        $adapter->shouldReceive('getDriver')->andReturn($driver)->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('groups')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpConnection()
    {
        $this->connection = \Mockery::mock(Connection::class);
    }

    /**
     * @test
     */
    public function testItShouldSaveNewGroup()
    {
        $newGroup = new Group();
        $newGroup->setOrganizationId('fizz-buzz')
            ->setTitle('rockin out 101')
            ->setType(TypeInterface::TYPE_CLASS);

        $this->assertNull($newGroup->getCreated());
        $this->assertNull($newGroup->getUpdated());

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) use (&$newGroup) {
                $this->assertNotNull($newGroup->getCreated());
                $this->assertNotNull($newGroup->getUpdated());
                $this->assertNotEmpty($newGroup->getGroupId());

                $this->assertTrue(is_array($data));

                $expected         = $newGroup->getArrayCopy();
                $expected['meta'] = '[]';
                unset($expected['depth']);
                unset($expected['deleted']);
                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals($expected, $data);

                return 1;
            })
            ->once();

        $this->assertTrue($this->groupService->createGroup($newGroup));
    }

    /**
     * @test
     */
    public function testItShouldUpdateExistingGroup()
    {
        $groupData = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'head'            => 1,
            'tail'            => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '2016-02-28',
        ];

        $group  = new Group($groupData);
        $result = new ResultSet();
        $result->initialize([$groupData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $groupData['group_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$group) {
                $this->assertEquals(['group_id' => $group->getGroupId()], $where);
                $expected         = $group->getArrayCopy();
                $expected['meta'] = '[]';
                unset($expected['deleted']);
                unset($expected['depth']);
                $this->assertArrayNotHasKey('deleted', $data);

                $this->assertEquals($expected, $data);
            });

        $this->assertTrue($this->groupService->updateGroup($group));
    }

    /**
     * @test
     */
    public function testItShouldFetchGroupById()
    {
        $groupData = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'head'            => 1,
            'tail'            => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '2016-02-28',
        ];

        $result = new ResultSet();
        $result->initialize([$groupData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $groupData['group_id']])
            ->andReturn($result);

        $this->assertInstanceOf('Group\Group', $this->groupService->fetchGroup($groupData['group_id']));
    }

    /**
     * @test
     */
    public function testItShouldFetchGroupByExternalId()
    {
        $groupData = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'head'            => 1,
            'tail'            => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '2016-02-28',
            'external_id'     => 'foo-bar',
            'network_id'      => 'baz-bat',
        ];

        $result = new ResultSet();
        $result->initialize([$groupData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['network_id' => $groupData['network_id'], 'external_id' => $groupData['external_id']])
            ->andReturn($result);

        $this->assertInstanceOf(
            'Group\Group',
            $this->groupService->fetchGroupByExternalId($groupData['network_id'], $groupData['external_id']),
            'GroupService did not return a group when fetching by external Id'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenGroupIsNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Group not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->groupService->fetchGroup('foo');
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenGroupIsNotFoundByExternalId()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Group not Found');

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->groupService->fetchGroupByExternalId('foo', 'bar');
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteByDefault()
    {
        $groupData = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'head'            => 1,
            'tail'            => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '',
        ];

        $group  = new Group($groupData);
        $result = new ResultSet();
        $result->initialize([$groupData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $groupData['group_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$group) {
                $this->assertEquals(['group_id' => $group->getGroupId()], $where);
                $this->assertNotEmpty($data['deleted']);
            });

        $this->assertTrue($this->groupService->deleteGroup($group));
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteWhenForced()
    {
        $groupData = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'head'            => 1,
            'tail'            => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '',
        ];

        $group  = new Group($groupData);
        $result = new ResultSet();
        $result->initialize([$groupData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $groupData['group_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('delete')
            ->andReturnUsing(function ($where) use (&$group) {
                $this->assertEquals(['group_id' => $group->getGroupId()], $where);
            });

        $this->assertTrue($this->groupService->deleteGroup($group, false));
    }

    /**
     * @test
     */
    public function testItShouldRebuildTreeWhenChildAddedForNewTree()
    {
        $parent = new Group([
            'group_id'        => 'parent',
            'head'            => 0,
            'tail'            => 0,
            'network_id'      => 'baz-bat',
            'title'           => 'rockin out 101',
            'type'            => TypeInterface::TYPE_CLASS,
            'organization_id' => 'fizz-buzz',
            'description'     => 'in this class you learn how to rock it',
            'external_id'     => 'baz-bat',
            'parent_id'       => 'manchuck',
        ]);

        $child = new Group();
        $child->setGroupId('child');

        $parentResult = new ResultSet();
        $parentResult->initialize([$parent->getArrayCopy()]);

        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $parent->getGroupId()])
            ->andReturn($parentResult)
            ->once();

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$parent) {
                $this->assertEquals(
                    ['group_id' => $parent->getGroupId()],
                    $where,
                    'Network update Where incorrect for parent'
                );
                $this->assertEquals(
                    ['head' => 1, 'tail' => 4],
                    $data
                );
            })
            ->once()
            ->ordered('update');

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$child) {
                $this->assertEquals(
                    ['group_id' => $child->getGroupId()],
                    $where,
                    'Network update Where incorrect for child'
                );
                $this->assertEquals(
                    ['head' => 2, 'tail' => 3, 'network_id' => 'baz-bat'],
                    $data,
                    'Network update incorrect for child'
                );
            })
            ->once()
            ->ordered('update');

        $this->groupService->attachChildToGroup($parent, $child);
        $this->assertEquals(
            'baz-bat',
            $child->getNetworkId(),
            'Network id was not set to the child'
        );
    }

    /**
     * @test
     */
    public function testItShouldRebuildTreeWhenChildAddedForExistingTree()
    {
        $this->connection->shouldReceive('beginTransaction')->once();
        $this->connection->shouldReceive('commit')->once();
        $this->connection->shouldReceive('rollback')->never();

        $parent = new Group();
        $parent->setGroupId('parent')
            ->setTitle('manchuck')
            ->setOrganizationId('org')
            ->setDescription('manchuck 2')
            ->setExternalId('foo-bar')
            ->setHead(1)
            ->setTail(2)
            ->setNetworkId('baz-bat')
            ->setType('school');

        $child = new Group();
        $child->setGroupId('child');

        $parentResult = new ResultSet();
        $parentResult->initialize([$parent->getArrayCopy()]);

        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $parent->getGroupId()])
            ->andReturn($parentResult)
            ->once();

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, $actualWhere) {

                $expectedWhere = new Where();
                $expectedWhere->addPredicate(new Operator('tail', '>', 1));
                $expectedWhere->addPredicate(new Operator('network_id', '=', 'baz-bat'));

                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $actualWhere);
                $this->assertEquals(
                    ['tail' => new Expression('tail + 2')],
                    $actualSet,
                    'Network shift update for tail incorrect'
                );

                $this->assertEquals(
                    $expectedWhere->getExpressionData(),
                    $actualWhere->getExpressionData(),
                    'Network shift for tail was built incorrectly'
                );

                return true;
            })
            ->once()
            ->ordered('update');

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, PredicateInterface $actualWhere) {

                $expectedWhere = new Where();
                $expectedWhere->addPredicate(new Operator('head', '>', 1));
                $expectedWhere->addPredicate(new Operator('network_id', '=', 'baz-bat'));

                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $actualWhere);
                $this->assertEquals(
                    ['head' => new Expression('head + 2')],
                    $actualSet,
                    'Network shift update for head incorrect'
                );
                $this->assertEquals(
                    $expectedWhere->getExpressionData(),
                    $actualWhere->getExpressionData(),
                    'Network shift for head was built incorrectly'
                );

                return true;
            })
            ->once()
            ->ordered('update');

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, PredicateInterface $actualWhere) {
                $expectedWhere = new Where();
                $expectedWhere->addPredicate(new Operator('group_id', '=', 'child'));

                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $actualWhere);
                $this->assertEquals(
                    ['head' => 2, 'tail' => 3, 'network_id' => 'baz-bat', 'parent_id' => 'parent'],
                    $actualSet,
                    'Network shift update for child incorrect'
                );
                $this->assertEquals(
                    $expectedWhere->getExpressionData(),
                    $actualWhere->getExpressionData(),
                    'Network shift Where for child group was built incorrectly'
                );

                return true;
            })
            ->once()
            ->ordered('update');

        $this->groupService->attachChildToGroup($parent, $child);
        $this->assertEquals(
            'baz-bat',
            $child->getNetworkId(),
            'Network id was not set to the child'
        );
    }
}
