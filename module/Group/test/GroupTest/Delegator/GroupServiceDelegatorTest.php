<?php

namespace GroupTest\Delegator;

use Group\Service\GroupServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Group\Group;
use Group\Delegator\GroupDelegator;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;

/**
 * Test GroupServiceDelegatorTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class GroupServiceDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var GroupDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var Group
     */
    protected $group;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\GroupService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator    = new GroupDelegator($this->groupService);
        $this->delegator->getEventManager()->clearListeners('save.group');
        $this->delegator->getEventManager()->clearListeners('fetch.group.post');
        $this->delegator->getEventManager()->clearListeners('fetch.all.groups');
        $this->delegator->getEventManager()->getSharedManager()->clearListeners(GroupDelegator::class);
        $this->delegator->getEventManager()->getSharedManager()->clearListeners(GroupServiceInterface::class);
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpGroup()
    {
        $this->group = new Group();
        $this->group->setGroupId(md5('foobar'));
        $this->group->setExternalId('foo-bar');
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams()
        ];
    }

    public function testItShouldCallSaveGroup()
    {
        $this->groupService->shouldReceive('saveGroup')
            ->with($this->group)
            ->andReturn(true)
            ->once();


        $this->delegator->saveGroup($this->group);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallSaveGroupWhenEventPrevents()
    {
        $this->groupService->shouldReceive('saveGroup')
            ->with($this->group)
            ->never();

        $this->delegator->getEventManager()->attach('save.group', function (Event $event) {
            $event->stopPropagation(true);
            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->saveGroup($this->group));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallFetchGroup()
    {
        $this->groupService->shouldReceive('fetchGroup')
            ->with($this->group->getGroupId())
            ->andReturn($this->group)
            ->once();

        $this->assertSame(
            $this->group,
            $this->delegator->fetchGroup($this->group->getGroupId())
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group',
                'target' => $this->groupService,
                'params' => ['group_id' => $this->group->getGroupId()]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'group_id' => $this->group->getGroupId()]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldCallFetchGroupByExternalId()
    {
        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->with($this->group->getExternalId())
            ->andReturn($this->group)
            ->once();

        $this->assertSame(
            $this->group,
            $this->delegator->fetchGroupByExternalId($this->group->getExternalId())
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.external',
                'target' => $this->groupService,
                'params' => ['external_id' => $this->group->getExternalId()]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.group.external.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'external_id' => $this->group->getExternalId()]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallFetchGroupAndReturnEventResult()
    {
        $this->groupService->shouldReceive('fetchGroup')
            ->with($this->group->getGroupId())
            ->andReturn($this->group)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.group', function (Event $event) {
            $event->stopPropagation(true);
            return $this->group;
        });

        $this->assertSame(
            $this->group,
            $this->delegator->fetchGroup($this->group->getGroupId())
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group',
                'target' => $this->groupService,
                'params' => ['group_id' => $this->group->getGroupId()]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallDeleteGroup()
    {
        $this->groupService->shouldReceive('deleteGroup')
            ->with($this->group, true)
            ->andReturn($this->group)
            ->once();

        $this->assertSame(
            $this->group,
            $this->delegator->deleteGroup($this->group)
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'soft' => true]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'soft' => true]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallDeleteGroupAndReturnEventResult()
    {
        $this->groupService->shouldReceive('deleteGroup')
            ->with($this->group, true)
            ->andReturn($this->group)
            ->never();

        $this->delegator->getEventManager()->attach('delete.group', function (Event $event) {
            $event->stopPropagation(true);
            return $this->group;
        });

        $this->assertSame(
            $this->group,
            $this->delegator->deleteGroup($this->group)
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'soft' => true]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->groupService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->once();

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.groups',
                'target' => $this->groupService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null]
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.groups.post',
                'target' => $this->groupService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null, 'groups' => $result]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldCallFetchAllWhenEventStops()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->groupService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.groups', function (Event $event) use (&$result) {
            $event->stopPropagation(true);
            return $result;
        });

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.groups',
                'target' => $this->groupService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null]
            ],
            $this->calledEvents[0]
        );
    }
}
