<?php

namespace UserTest\Delegator;

use Application\Exception\DuplicateEntryException;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use User\Delegator\CheckUserListener;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\EventManager\Event;

/**
 * Test CheckUserListenerTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class CheckUserListenerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\User\Service\UserService
     */
    protected $userService;

    /**
     * @var Adult
     */
    protected $user;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->userService = \Mockery::mock('\User\Service\UserService');
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Adult();
        $this->user->setUserId(md5('foobar'));
    }

    public function testItShouldNotThrowExceptionWhenUserIsOk()
    {
        $this->user->setEmail('chuck@manchuck.com');
        $this->user->setUserName('manchuck');

        $event = new Event();
        $event->setTarget($this->userService);
        $event->setParam('user', $this->user);

        $this->userService->shouldReceive('fetchAll')
            ->once()
            ->andReturnUsing(function ($predicate, $pageParam) {
                $expectedPredicate = new PredicateSet([
                    new PredicateSet([
                        new Operator('email', Operator::OP_EQ, 'chuck@manchuck.com'),
                        new Operator('username', Operator::OP_EQ, 'manchuck')
                    ], PredicateSet::OP_OR),

                    new Operator('user_id', Operator::OP_NE, md5('foobar'))
                ]);

                $this->assertEquals($expectedPredicate, $predicate);
                $this->assertFalse($pageParam);

                return new \ArrayIterator([]);
            });

        $listener = new CheckUserListener();
        $this->assertFalse($event->propagationIsStopped());
        $listener->checkUniqueFields($event);
        $this->assertFalse($event->propagationIsStopped());
    }

    public function testItShouldThrowExceptionWhenUserHadDuplicateEmailOrName()
    {
        $this->user->setEmail('chuck@manchuck.com');
        $this->user->setUserName('manchuck');

        $event = new Event();
        $event->setTarget($this->userService);
        $event->setParam('user', $this->user);

        $this->userService->shouldReceive('fetchAll')
            ->once()
            ->andReturnUsing(function ($predicate, $pageParam) {
                $expectedPredicate = new PredicateSet([
                    new PredicateSet([
                        new Operator('email', Operator::OP_EQ, 'chuck@manchuck.com'),
                        new Operator('username', Operator::OP_EQ, 'manchuck')
                    ], PredicateSet::OP_OR),

                    new Operator('user_id', Operator::OP_NE, md5('foobar'))
                ]);

                $this->assertEquals($expectedPredicate, $predicate);
                $this->assertFalse($pageParam);

                return new \ArrayIterator(['foo']);
            });

        $listener = new CheckUserListener();
        $this->assertFalse($event->propagationIsStopped());
        $thrown = false;
        try {
            $listener->checkUniqueFields($event);
        } catch (DuplicateEntryException $dupeUser) {
            $thrown = true;
            $this->assertEquals('Invalid user', $dupeUser->getMessage());
        }

        $this->assertTrue($event->propagationIsStopped());
    }

    public function testItShouldDoNothingWhenUserServiceNotSet()
    {
        $event = new Event();
        $event->setParam('user', $this->user);

        $this->userService->shouldReceive('fetchAll')
            ->never();

        $listener = new CheckUserListener();
        $this->assertFalse($event->propagationIsStopped());
        $listener->checkUniqueFields($event);
        $this->assertFalse($event->propagationIsStopped());
    }

    public function testItShouldDoNothingWhenUserNotSet()
    {
        $event = new Event();
        $event->setTarget($this->userService);

        $this->userService->shouldReceive('fetchAll')
            ->never();

        $listener = new CheckUserListener();
        $this->assertFalse($event->propagationIsStopped());
        $listener->checkUniqueFields($event);
        $this->assertFalse($event->propagationIsStopped());
    }

}
