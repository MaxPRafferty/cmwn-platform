<?php

namespace GroupTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Group\Group;
use Group\Service\GroupService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\Predicate as Where;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Json\Json;

/**
 * Test GroupServiceTest
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
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('groups')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = new GroupService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->groupService->fetchAll(null);
        $this->assertInstanceOf('\Zend\Paginator\Adapter\AdapterInterface', $result);
    }

    /**
     * @test
     */
    public function testItShouldReturnIteratorOnFetchAllWithNoWhereAndNotPaginating()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $where);
                return new \ArrayIterator([]);
            })
            ->once();

        $result = $this->groupService->fetchAll(null, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    /**
     * @test
     */
    public function testItShouldReturnIteratorPassWhereWhenGivenWhereAndNotPaginating()
    {
        $expectedWhere = new Where();
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($where) use (&$expectedWhere) {
                /** @var \Zend\Db\Sql\Predicate\Predicate $where */
                $this->assertSame($expectedWhere, $where);
                return new \ArrayIterator([]);

            })
            ->once();

        $result = $this->groupService->fetchAll($expectedWhere, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    /**
     * @test
     */
    public function testItShouldSaveNewGroup()
    {
        $newGroup = new Group();

        $this->assertNull($newGroup->getCreated());
        $this->assertNull($newGroup->getUpdated());
        $this->assertEmpty($newGroup->getGroupId());

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) use (&$newGroup) {
                $this->assertNotNull($newGroup->getCreated());
                $this->assertNotNull($newGroup->getUpdated());
                $this->assertNotEmpty($newGroup->getGroupId());

                $this->assertTrue(is_array($data));

                $expected = $newGroup->getArrayCopy();
                $expected['meta'] = '[]';
                unset($expected['depth']);
                unset($expected['deleted']);
                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals($expected, $data);
                return 1;
            })
            ->once();

        $this->assertTrue($this->groupService->saveGroup($newGroup));
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
            'tail'           => 2,
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
                $expected = $group->getArrayCopy();
                $expected['meta'] = '[]';
                unset($expected['deleted']);
                unset($expected['depth']);
                $this->assertArrayNotHasKey('deleted', $data);

                $this->assertEquals($expected, $data);

            });

        $this->assertTrue($this->groupService->saveGroup($group));
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
            'tail'           => 2,
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
    public function testItShouldFetchGroupByExternalIdId()
    {
        $groupData = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'head'            => 1,
            'tail'           => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '2016-02-28',
            'external_id'     => 'foo-bar'
        ];

        $result = new ResultSet();
        $result->initialize([$groupData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['external_id' => $groupData['external_id']])
            ->andReturn($result);

        $this->assertInstanceOf('Group\Group', $this->groupService->fetchGroupByExternalId($groupData['external_id']));
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenGroupIsNotFound()
    {
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'Group not Found'
        );

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
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'Group not Found'
        );

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->groupService->fetchGroupByExternalId('foo');
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
            'tail'           => 2,
            'depth'           => 3,
            'created'         => '2016-02-28',
            'updated'         => '2016-02-28',
            'deleted'         => '',
        ];

        $group   = new Group($groupData);
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
            'tail'           => 2,
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
        $this->markTestSkipped('Change GroupService interface to have create and save');
        $parent = new Group([
            'group_id' => 'parent',
            'head'     => 0,
            'tail'     => 0,
        ]);

        $child = new Group();
        $child->setGroupId('child');

        $data = $child->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);

        unset($data['depth']);
        unset($data['deleted']);

        $parentResult = new ResultSet();
        $parentResult->initialize([$parent->getArrayCopy()]);

        $childResult = new ResultSet();
        $childResult->initialize([$child->getArrayCopy()]);

        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $child->getGroupId()])
            ->andReturn($childResult);

        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $parent->getGroupId()])
            ->andReturn($parentResult);

        $this->tableGateway->shouldReceive('update')
            ->with(
                $data,
                ['group_id' => 'child']
            )
            ->once();

        $this->tableGateway->shouldReceive('update')
            ->with(
                ['head' => 1, 'tail' => 4],
                ['group_id' => 'parent']
            )
            ->once();

        $this->tableGateway->shouldReceive('update')
            ->with(
                ['head' => 2, 'tail' => 3],
                ['group_id' => 'child']
            )
            ->once();

        $this->groupService->addChildToGroup($parent, $child);
    }

    /**
     * @test
     */
    public function testItShouldRebuildTreeWhenChildAddedForExistingTree()
    {
        $this->markTestSkipped('Change GroupService interface to have create and save');
        $parent = new Group([
            'group_id'        => 'parent',
            'organization_id' => 'org',
            'head'            => 1,
            'tail'           => 2,
        ]);

        $child = new Group();
        $child->setGroupId('child');

        $result = new ResultSet();
        $result->initialize([$parent->getArrayCopy()]);
        $this->tableGateway->shouldReceive('select')
            ->with(['group_id' => $parent->getGroupId()])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, $actualWhere) {

                $expectedWhere = new Where();
                $expectedWhere->addPredicate(new Operator('tail', '>', 1));
                $expectedWhere->addPredicate(new Operator('organization_id', '=', 'org'));

                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $actualWhere);
                $this->assertEquals(['tail' => new Expression('tail + 2')], $actualSet);
                $this->assertEquals($expectedWhere->getExpressionData(), $actualWhere->getExpressionData());
                return true;
            })
            ->times(1)
            ->ordered();

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, PredicateInterface $actualWhere) {

                $expectedWhere = new Where();
                $expectedWhere->addPredicate(new Operator('head', '>', 1));
                $expectedWhere->addPredicate(new Operator('organization_id', '=', 'org'));
                $expectedWhere->addPredicate(new Operator('group_id', '!=', 'parent'));

                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $actualWhere);
                $this->assertEquals(['head' => new Expression('head + 2')], $actualSet);
                $this->assertEquals($expectedWhere->getExpressionData(), $actualWhere->getExpressionData());
                return true;
            })
            ->times(1)
            ->ordered();

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($actualSet, PredicateInterface $actualWhere) {
                $expectedWhere = new Where();
                $expectedWhere->addPredicate(new Operator('group_id', '=', 'child'));

                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $actualWhere);
                $this->assertEquals(['head' => 2, 'tail' => 3], $actualSet);
                $this->assertEquals($expectedWhere->getExpressionData(), $actualWhere->getExpressionData());
                return true;
            })
            ->times(1)
            ->ordered();

        $this->groupService->addChildToGroup($parent, $child);
    }
}
