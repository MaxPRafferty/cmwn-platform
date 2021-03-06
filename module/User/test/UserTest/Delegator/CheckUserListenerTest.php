<?php

namespace UserTest\Delegator;

use Application\Exception\DuplicateEntryException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Adult;
use User\Delegator\CheckUserListener;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\EventManager\Event;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * Test CheckUserListenerTest
 *
 * @group User
 * @group UserService
 * @group Service
 * @group Validator
 */
class CheckUserListenerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

    /**
     * @test
     */
    public function testItShouldNotThrowExceptionWhenUserIsOk()
    {
        $this->user->setEmail('chuck@manchuck.com');
        $this->user->setUserName('manchuck');

        $event = new Event();
        $event->setTarget($this->userService);
        $event->setParam('user', $this->user);

        $this->userService->shouldReceive('fetchAll')
            ->once()
            ->andReturnUsing(function ($predicate) {
                $expectedPredicate = new PredicateSet([
                    new PredicateSet([
                        new Operator('email', Operator::OP_EQ, 'chuck@manchuck.com'),
                        new Operator('username', Operator::OP_EQ, 'manchuck'),
                        new Operator('normalized_username', Operator::OP_EQ, 'manchuck')
                    ], PredicateSet::OP_OR),

                    new Operator('user_id', Operator::OP_NE, md5('foobar'))
                ]);

                $this->assertEquals($expectedPredicate, $predicate);

                return new ArrayAdapter([]);
            });

        $listener = new CheckUserListener();
        $this->assertFalse($event->propagationIsStopped());
        $listener->checkUniqueFields($event);
        $this->assertFalse($event->propagationIsStopped());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserHadDuplicateEmailOrName()
    {
        $this->user->setEmail('chuck@manchuck.com');
        $this->user->setUserName('manchuck');

        $event = new Event();
        $event->setTarget($this->userService);
        $event->setParam('user', $this->user);

        $this->userService->shouldReceive('fetchAll')
            ->once()
            ->andReturnUsing(function ($predicate) {
                $expectedPredicate = new PredicateSet([
                    new PredicateSet([
                        new Operator('email', Operator::OP_EQ, 'chuck@manchuck.com'),
                        new Operator('username', Operator::OP_EQ, 'manchuck'),
                        new Operator('normalized_username', Operator::OP_EQ, 'manchuck')
                    ], PredicateSet::OP_OR),

                    new Operator('user_id', Operator::OP_NE, md5('foobar'))
                ]);

                $this->assertEquals($expectedPredicate, $predicate);

                return new ArrayAdapter(['foo']);
            });

        $listener = new CheckUserListener();
        $this->assertFalse($event->propagationIsStopped());
        try {
            $listener->checkUniqueFields($event);
        } catch (DuplicateEntryException $dupeUser) {
            $this->assertEquals(
                'Invalid Username:(' . $this->user->getUserName() . ') or email(' . $this->user->getEmail() . ')',
                $dupeUser->getMessage()
            );
        }

        $this->assertTrue($event->propagationIsStopped());
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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
