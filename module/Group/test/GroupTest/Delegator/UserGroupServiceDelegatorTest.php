<?php

namespace GroupTest\Delegator;

use Application\Exception\NotFoundException;
use Group\Delegator\UserGroupServiceDelegator;
use Group\Service\UserGroupService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Org\Organization;
use PHPUnit\Framework\TestCase;
use Group\Group;
use User\Adult;
use User\User;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * Class UserGroupServiceDelegatorTest
 *
 * @group User
 * @group Group
 * @group Delegator
 * @group UserGroupService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserGroupServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Group\Service\UserGroupService
     */
    protected $groupService;

    /**
     * @var UserGroupServiceDelegator
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
     * @var User
     */
    protected $user;

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->delegator    = new UserGroupServiceDelegator(
            $this->groupService,
            new EventManager()
        );
        $this->calledEvents = [];
        $this->delegator->getEventManager()->clearListeners('fetch.group.users');
        $this->delegator->getEventManager()->clearListeners('fetch.org.users');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = \Mockery::mock(UserGroupService::class);
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
     * @before
     */
    public function setUpGroup()
    {
        $this->group = new Group();
        $this->group->setGroupId(md5('foobar'));
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Adult();
        $this->user->setUserId('foobar');
    }

    /**
     * @test
     */
    public function testItShouldCallAttachUser()
    {
        $this->groupService->shouldReceive('attachUserToGroup')
            ->with($this->group, $this->user, 'teacher')
            ->once();

        $this->delegator->attachUserToGroup($this->group, $this->user, 'teacher');

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.user',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher'],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'attach.user.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher'],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachUserWhenEventStopped()
    {
        $this->delegator->getEventManager()->attach('attach.user', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->groupService->shouldReceive('attachUserToGroup')
            ->never();

        $this->delegator->attachUserToGroup($this->group, $this->user, 'teacher');

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.user',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher'],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallAttachPostWhenThereIsAnError()
    {
        $testException = new \Exception();

        $this->groupService->shouldReceive('attachUserToGroup')
            ->once()
            ->andThrow($testException);

        $this->delegator->attachUserToGroup($this->group, $this->user, 'teacher');

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'attach.user',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher'],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'attach.user.error',
                'target' => $this->groupService,
                'params' => [
                    'group'     => $this->group,
                    'user'      => $this->user,
                    'role'      => 'teacher',
                    'exception' => $testException,
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDetachUser()
    {
        $this->groupService->shouldReceive('detachUserFromGroup')
            ->with($this->group, $this->user)
            ->once();

        $this->delegator->detachUserFromGroup($this->group, $this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'detach.user',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'detach.user.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchUsersForGroup()
    {
        $return = new ArrayAdapter([]);
        $this->groupService->shouldReceive('fetchUsersForGroup')
            ->once()
            ->andReturn($return);

        $this->delegator->fetchUsersForGroup($this->group, null, $this->user);

        $where = new Where();
        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.users',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'where' => $where],
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.group.users.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'where' => $where, 'results' => $return],
            ],
            $this->calledEvents[1],
            'Post event for fetchUsersForGroup Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchUsersForOrg()
    {
        $return = new ArrayAdapter([]);
        $org    = new Organization();
        $this->groupService->shouldReceive('fetchUsersForOrg')
            ->once()
            ->andReturn($return);

        $this->delegator->fetchUsersForOrg($org, null, $this->user);
        $where = new Where();

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org.users',
                'target' => $this->groupService,
                'params' => ['organization' => $org, 'where' => $where],
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForOrg Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.org.users.post',
                'target' => $this->groupService,
                'params' => ['organization' => $org, 'where' => $where, 'result' => $return],
            ],
            $this->calledEvents[1],
            'Post event for fetchUsersForOrg Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchUsersForGroupWhenEventStops()
    {
        $return = new ArrayAdapter([]);
        $this->groupService->shouldReceive('fetchUsersForGroup')->never();

        $this->delegator->getEventManager()->attach('fetch.group.users', function (Event $event) use ($return) {
            $event->stopPropagation(true);

            return $return;
        });

        $this->delegator->fetchUsersForGroup($this->group, null, $this->user);
        $where = new Where();

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.users',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'where' => $where],
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup was not fired for fetch users for group'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchUsersForOrgWhenEventStops()
    {
        $return = new ArrayAdapter([]);
        $org    = new Organization();
        $this->groupService->shouldReceive('fetchUsersForOrg')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.org.users', function (Event $event) use ($return) {
            $event->stopPropagation(true);

            return $return;
        });

        $this->delegator->fetchUsersForOrg($org, null, $this->user);
        $where = new Where();

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org.users',
                'target' => $this->groupService,
                'params' => ['organization' => $org, 'where' => $where],
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchUsersPostForGroupErrorHappens()
    {
        $exception = new NotFoundException();
        $this->groupService->shouldReceive('fetchUsersForGroup')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchUsersForGroup($this->group, null, $this->user);
            $this->fail(UserGroupServiceDelegator::class . ' did not re-throw the exception');
        } catch (\Exception $actual) {
            $this->assertSame($actual, $exception);
        }
        $where = new Where();

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.users',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'where' => $where],
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup was not fired for fetch users for group'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.group.users.error',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'where' => $where, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            'Pre event for fetchUsersForGroup was not fired for fetch users for group'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchUsersPostForOrgWhenErrorHappens()
    {
        $org       = new Organization();
        $exception = new NotFoundException();
        $this->groupService->shouldReceive('fetchUsersForOrg')
            ->andThrow($exception);

        try {
            $this->delegator->fetchUsersForOrg($org, null, $this->user);
            $this->fail('Exception was not thrown');
        } catch (\Exception $actual) {
            $this->assertSame($exception, $actual);
        }
        $where = new Where();

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org.users',
                'target' => $this->groupService,
                'params' => ['organization' => $org, 'where' => $where],
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForOrg Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.org.users.error',
                'target' => $this->groupService,
                'params' => ['organization' => $org, 'where' => $where, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            'Pre event for fetchUsersForOrg Is incorrect'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDetachUserWhenEventStopped()
    {
        $this->delegator->getEventManager()->attach('detach.user', function (Event $event) {
            $event->stopPropagation(true);

            return false;
        });

        $this->groupService->shouldReceive('detachUserFromGroup')
            ->never();

        $this->delegator->detachUserFromGroup($this->group, $this->user);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'detach.user',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDetachPostWhenThereIsAnError()
    {
        $testException = new \Exception();

        $this->groupService->shouldReceive('detachUserFromGroup')
            ->once()
            ->andThrow($testException);

        $this->delegator->detachUserFromGroup($this->group, $this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'detach.user',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'detach.user.error',
                'target' => $this->groupService,
                'params' => [
                    'group'     => $this->group,
                    'user'      => $this->user,
                    'exception' => $testException,
                ],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllUsersForUser()
    {
        $return = new ArrayAdapter([]);
        $this->groupService->shouldReceive('fetchAllUsersForUser')
            ->with($this->user, null, null)
            ->andReturn($return)
            ->once();

        $this->delegator->fetchAllUsersForUser($this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.users',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.users.post',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null, 'result' => $return],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllUsersForUserWhenEventStopped()
    {
        $return = new ArrayAdapter([]);
        $this->delegator->getEventManager()->attach('fetch.all.user.users', function (Event $event) use ($return) {
            $event->stopPropagation(true);

            return $return;
        });

        $this->groupService->shouldReceive('fetchAllUsersForUser')
            ->never();

        $this->delegator->fetchAllUsersForUser($this->user);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.users',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllUsersForUserWhenException()
    {
        $exception = new \Exception();
        $this->groupService->shouldReceive('fetchAllUsersForUser')
            ->with($this->user, null, null)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchAllUsersForUser($this->user);
            $this->fail('Exception was not thrown');
        } catch (\Exception $actual) {
            $this->assertSame($exception, $actual);
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.users',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.user.users.error',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'where' => null, 'prototype' => null, 'exception' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchOrganizationsForUser()
    {
        $resultSet = new ArrayAdapter([]);
        $this->groupService->shouldReceive('fetchOrganizationsForUser')
            ->with($this->user, null)
            ->andReturn($resultSet)
            ->once();

        $this->delegator->fetchOrganizationsForUser($this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.orgs',
                'target' => $this->groupService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.orgs.post',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'result' => $resultSet],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchOrganizationsForUserWhenEventStopped()
    {
        $return = new ArrayAdapter([]);
        $this->delegator->getEventManager()->attach('fetch.user.orgs', function (Event $event) use ($return) {
            $event->stopPropagation(true);

            return $return;
        });

        $this->groupService->shouldReceive('fetchOrganizationsForUser')
            ->never();

        $this->delegator->fetchOrganizationsForUser($this->user);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.orgs',
                'target' => $this->groupService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchOrganizationsForUserWhenException()
    {
        $exception = new \Exception();
        $this->groupService->shouldReceive('fetchOrganizationsForUser')
            ->with($this->user, null)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchOrganizationsForUser($this->user);
            $this->fail('exception was no thrown');
        } catch (\Exception $actual) {
            $this->assertSame($exception, $actual);
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.orgs',
                'target' => $this->groupService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.orgs.error',
                'target' => $this->groupService,
                'params' => ['user' => $this->user, 'exception' => $exception],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchGroupsForUser()
    {
        $resultSet = new ArrayAdapter([]);
        $this->groupService->shouldReceive('fetchGroupsForUser')
            ->with($this->user, null, null)
            ->andReturn($resultSet)
            ->once();

        $this->delegator->fetchGroupsForUser($this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.group',
                'target' => $this->groupService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.user.group.post',
                'target' => $this->groupService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchGroupsForUserWhenEventStopped()
    {
        $return = new ArrayAdapter([]);
        $this->delegator->getEventManager()->attach('fetch.user.group', function (Event $event) use ($return) {
            $event->stopPropagation(true);

            return $return;
        });

        $this->groupService->shouldReceive('fetchGroupsForUser')
            ->never();

        $this->delegator->fetchGroupsForUser($this->user);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.user.group',
                'target' => $this->groupService,
                'params' => ['user' => $this->user],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchGroupsForUserWhenException()
    {
        $exception = new \Exception();
        $this->groupService->shouldReceive('fetchGroupsForUser')
            ->with($this->user, null, null)
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchGroupsForUser($this->user);
            $this->fail("Did not rethrow exception");
        } catch (\Exception $e) {
            $this->assertEquals(2, count($this->calledEvents));
            $this->assertEquals(
                [
                    'name'   => 'fetch.user.group',
                    'target' => $this->groupService,
                    'params' => ['user' => $this->user],
                ],
                $this->calledEvents[0]
            );
            $this->assertEquals(
                [
                    'name'   => 'fetch.user.group.error',
                    'target' => $this->groupService,
                    'params' => ['user' => $this->user, 'exception' => $exception],
                ],
                $this->calledEvents[1]
            );
        }
    }
}
