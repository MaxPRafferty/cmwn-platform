<?php

namespace GroupTest\Delegator;

use Group\Service\GroupServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Group\Group;
use Group\Delegator\GroupDelegator;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;

/**
 * Test GroupServiceDelegatorTest
 *
 * @group Group
 * @group Delegator
 * @group GroupService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function setUpDelegator()
    {
        $events = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new GroupDelegator($this->groupService, $events);
        $this->delegator->getEventManager()->clearListeners('save.group');
        $this->delegator->getEventManager()->clearListeners('fetch.group.post');
        $this->delegator->getEventManager()->clearListeners('fetch.all.groups');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = \Mockery::mock(GroupServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpGroup()
    {
        $this->group = new Group();
        $this->group->setGroupId(md5('foobar'));
        $this->group->setNetworkId('baz-bat');
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
            'params' => $event->getParams(),
        ];
    }

    /**
     * @test
     */
    public function testItShouldCallCreateGroup()
    {
        $this->groupService->shouldReceive('createGroup')
            ->with($this->group)
            ->andReturn(true)
            ->once();

        $this->delegator->createGroup($this->group);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCreateGroupWithException()
    {
        $exception = new \Exception('Im borked');
        $this->groupService->shouldReceive('createGroup')
            ->with($this->group)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->createGroup($this->group);
            $this->fail('Group Service delegator did not re-throw exception on createGroup');
        } catch (\Exception $actualException) {
            $this->assertSame(
                $exception,
                $actualException,
                'Group service delegator threw wrong exception on createGroup'
            );
        }

        $this->assertEquals(
            [
                'name'   => 'save.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[0],
            'save.group was not triggered on GroupServiceDelegator'
        );

        $this->assertEquals(
            [
                'name'   => 'save.group.error',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            'save.group.error was not triggered on GroupServiceDelegator'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallCreateGroupWhenEventPrevents()
    {
        $this->groupService->shouldReceive('createGroup')
            ->with($this->group)
            ->never();

        $this->delegator->getEventManager()->attach('save.group', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->createGroup($this->group));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateGroup()
    {
        $this->groupService->shouldReceive('updateGroup')
            ->with($this->group)
            ->andReturn(true)
            ->once();

        $this->delegator->updateGroup($this->group);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'update.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateGroupWithException()
    {
        $exception = new \Exception('Im broken');
        $this->groupService->shouldReceive('updateGroup')
            ->with($this->group)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->updateGroup($this->group);
            $this->fail('Exception was not thrown for updateGroup on Group Service Delegator');
        } catch (\Exception $actualException) {
            $this->assertSame($exception, $actualException, 'Group Service delegator did not re-throw exception');
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            'Incorrect number of events triggered for Group Service Delegator on exceptionr'
        );
        $this->assertEquals(
            [
                'name'   => 'update.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'update.group.error',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'exception' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallUpdateGroupWhenEventPrevents()
    {
        $this->groupService->shouldReceive('updateGroup')
            ->with($this->group)
            ->never();

        $this->delegator->getEventManager()->attach('update.group', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->updateGroup($this->group));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'update.group',
                'target' => $this->groupService,
                'params' => ['group' => $this->group],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
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
                'params' => ['group_id' => $this->group->getGroupId()],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'group_id' => $this->group->getGroupId()],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchGroupByExternalId()
    {
        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->with($this->group->getNetworkId(), $this->group->getExternalId())
            ->andReturn($this->group)
            ->once();

        $this->assertSame(
            $this->group,
            $this->delegator->fetchGroupByExternalId($this->group->getNetworkId(), $this->group->getExternalId())
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.external',
                'target' => $this->groupService,
                'params' => [
                    'network_id'  => $this->group->getNetworkId(),
                    'external_id' => $this->group->getExternalId(),
                ],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.group.external.post',
                'target' => $this->groupService,
                'params' => [
                    'group'       => $this->group,
                    'network_id'  => $this->group->getNetworkId(),
                    'external_id' => $this->group->getExternalId(),
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
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
                'params' => ['group_id' => $this->group->getGroupId()],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
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
                'params' => ['group' => $this->group, 'soft' => true],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.group.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'soft' => true],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
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
                'params' => ['group' => $this->group, 'soft' => true],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
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
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.groups.post',
                'target' => $this->groupService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null, 'groups' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
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
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }
}
