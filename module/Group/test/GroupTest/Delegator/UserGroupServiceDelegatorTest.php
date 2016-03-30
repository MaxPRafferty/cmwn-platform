<?php

namespace GroupTest\Delegator;

use Application\Exception\NotFoundException;
use Group\Delegator\UserGroupServiceDelegator;
use Mockery\Matcher\Not;
use \PHPUnit_Framework_TestCase as TestCase;
use Group\Group;
use User\Adult;
use User\User;
use Zend\EventManager\Event;

/**
 * Class UserGroupServiceDelegatorTest
 * @package GroupTest\Delegator
 */
class UserGroupServiceDelegatorTest extends TestCase
{
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
    public function setUpService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\UserGroupService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator    = new UserGroupServiceDelegator($this->groupService);
        $this->delegator->getEventManager()->clearListeners('save.group');
        $this->delegator->getEventManager()->clearListeners('fetch.group.post');
        $this->delegator->getEventManager()->clearListeners('fetch.all.groups');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
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
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher']
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'attach.user.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher']
            ],
            $this->calledEvents[1]
        );
    }
    
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
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher']
            ],
            $this->calledEvents[0]
        );
    }

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
                'params' => ['group' => $this->group, 'user' => $this->user, 'role' => 'teacher']
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
                    'exception' => $testException]
            ],
            $this->calledEvents[1]
        );
    }

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
                'params' => ['group' => $this->group, 'user' => $this->user]
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'detach.user.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'user' => $this->user]
            ],
            $this->calledEvents[1]
        );
    }


    public function testItShouldCallFetchUsersForGroup()
    {
        $this->groupService->shouldReceive('fetchUsersForGroup')
            ->with($this->group, $this->user)
            ->once();

        $this->delegator->fetchUsersForGroup($this->group, $this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.users',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.group.users.post',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[1],
            'Post event for fetchUsersForGroup Is incorrect'
        );
    }

    public function testItShouldCallFetchUsersForOrg()
    {
        $this->groupService->shouldReceive('fetchUsersForOrg')
            ->with($this->group, $this->user)
            ->once();

        $this->delegator->fetchUsersForOrg($this->group, $this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org.users',
                'target' => $this->groupService,
                'params' => ['organization' => $this->group]
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.org.users.post',
                'target' => $this->groupService,
                'params' => ['organization' => $this->group]
            ],
            $this->calledEvents[1],
            'Post event for fetchUsersForGroup Is incorrect'
        );
    }

    public function testItShouldNotCallFetchUsersForGroupWhenEventStops()
    {
        $this->groupService->shouldReceive('fetchUsersForGroup')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.group.users', function (Event $event) {
            $event->stopPropagation(true);
        });

        $this->delegator->fetchUsersForGroup($this->group, $this->user);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.users',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup was not fired for fetch users for group'
        );
    }

    public function testItShouldNotCallFetchUsersForOrgWhenEventStops()
    {
        $this->groupService->shouldReceive('fetchUsersForOrg')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.org.users', function (Event $event) {
            $event->stopPropagation(true);
        });

        $this->delegator->fetchUsersForOrg($this->group, $this->user);

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org.users',
                'target' => $this->groupService,
                'params' => ['organization' => $this->group]
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup Is incorrect'
        );
    }

    public function testItShouldNotCallFetchUsersPostForGroupErrorHappens()
    {
        $exception = new NotFoundException();
        $this->groupService->shouldReceive('fetchUsersForGroup')
            ->andThrow($exception)
            ->once();

        $this->delegator->fetchUsersForGroup($this->group, $this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.group.users',
                'target' => $this->groupService,
                'params' => ['group' => $this->group]
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup was not fired for fetch users for group'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.group.users.error',
                'target' => $this->groupService,
                'params' => ['group' => $this->group, 'exception' => $exception]
            ],
            $this->calledEvents[1],
            'Pre event for fetchUsersForGroup was not fired for fetch users for group'
        );
    }

    public function testItShouldNotCallFetchUsersPostForOrgWhenErrorHappens()
    {
        $exception = new NotFoundException();
        $this->groupService->shouldReceive('fetchUsersForOrg')
            ->andThrow($exception);

        $this->delegator->fetchUsersForOrg($this->group, $this->user);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org.users',
                'target' => $this->groupService,
                'params' => ['organization' => $this->group]
            ],
            $this->calledEvents[0],
            'Pre event for fetchUsersForGroup Is incorrect'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.org.users.error',
                'target' => $this->groupService,
                'params' => ['organization' => $this->group, 'exception' => $exception]
            ],
            $this->calledEvents[1],
            'Pre event for fetchUsersForGroup Is incorrect'
        );
    }

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
                'params' => ['group' => $this->group, 'user' => $this->user]
            ],
            $this->calledEvents[0]
        );
    }

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
                'params' => ['group' => $this->group, 'user' => $this->user]
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
                    'exception' => $testException]
            ],
            $this->calledEvents[1]
        );
    }
}
