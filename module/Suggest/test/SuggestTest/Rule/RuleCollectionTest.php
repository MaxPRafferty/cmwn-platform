<?php

namespace SuggestTest\Rule;

use Aws\Api\Service;
use Friend\FriendInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Suggest\InvalidRuleException;
use Suggest\Rule\FriendRule;
use Suggest\Rule\MeRule;
use Suggest\Rule\RuleCollection;
use Suggest\Rule\TypeRule;
use Suggest\SuggestionCollection;
use User\Adult;
use User\Child;
use Zend\ServiceManager\ServiceManager;

/**
 * Test RuleCollectionTest
 *
 * @group User
 * @group Suggest
 * @group Rule
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleCollectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ServiceManager
     */
    protected $service;

    /**
     * @var \Mockery\MockInterface|\Friend\Service\FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var array
     */
    protected $rulesConfig = [
        'me-rule'     => \Suggest\Rule\MeRule::class,
        'type-rule'   => \Suggest\Rule\TypeRule::class,
        'friend-rule' => \Suggest\Rule\FriendRule::class,
    ];

    /**
     * @var RuleCollection
     */
    protected $ruleCollection;

    /**
     * @before
     */
    public function setUpRuleCollection()
    {
        $this->ruleCollection = new RuleCollection($this->service, $this->rulesConfig);
    }

    /**
     * @before
     */
    public function setUpServiceLocator()
    {
        $this->service = new ServiceManager();
        $friendRule    = new FriendRule($this->friendService);
        $this->service->setService(FriendRule::class, $friendRule);
        $this->service->setInvokableClass(MeRule::class, MeRule::class);
        $this->service->setInvokableClass(TypeRule::class, TypeRule::class);
    }

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = \Mockery::mock(FriendServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldRemoveCorrectUsers()
    {
        $currentUser    = new Child(['user_id' => 'current_user']);
        $notFriends1    = new Child(['user_id' => 'not_friends_1']);
        $pendingFriends = new Child(['user_id' => 'pending_friends']);
        $notFriends2    = new Child(['user_id' => 'not_friends_2']);
        $waitingApprove = new Child(['user_id' => 'waiting_approve']);
        $alreadyFriends = new Child(['user_id' => 'already_friends']);
        $teacher        = new Adult(['user_id' => 'english_teacher']);

        $collection = new SuggestionCollection();
        $collection->append($notFriends1);
        $collection->append($pendingFriends);
        $collection->append($waitingApprove);
        $collection->append($notFriends2);
        $collection->append($alreadyFriends);
        $collection->append($currentUser);
        $collection->append($teacher);

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $notFriends1)
            ->andThrow(new NotFriendsException())
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $pendingFriends)
            ->andReturn(FriendInterface::PENDING)
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $notFriends2)
            ->andThrow(new NotFriendsException())
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $waitingApprove)
            ->andReturn(FriendInterface::REQUESTED)
            ->ordered('friend_rule');

        $this->friendService->shouldReceive('fetchFriendStatusForUser')
            ->with($currentUser, $alreadyFriends)
            ->andReturn(FriendInterface::FRIEND)
            ->ordered('friend_rule');

        $this->ruleCollection->apply($collection, $currentUser);

        $this->assertEquals(
            ['not_friends_1' => $notFriends1, 'not_friends_2' => $notFriends2],
            $collection->getArrayCopy(),
            'Incorrect suggested friends after rules applied'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenMissingRuleFromService()
    {
        $services   = new ServiceManager();
        $config     = ['foo-bar' => 'foobar'];
        $rules      = new RuleCollection($services, $config);
        $collection = new SuggestionCollection();
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage('Missing rule: "foobar" from services');

        $rules->apply($collection, new Child());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWithInvalidRule()
    {
        $services = new ServiceManager();
        $config   = ['foo-bar' => 'foobar'];
        $services->setService('foobar', new \stdClass());
        $rules      = new RuleCollection($services, $config);
        $collection = new SuggestionCollection();
        $this->expectException(InvalidRuleException::class);
        $this->expectExceptionMessage('Invalid Rule Provided');

        $this->assertEmpty($rules->apply($collection, new Child()));
    }

    /**
     * @test
     */
    public function testItShouldNotCreateRulesTwice()
    {
        $services = \Mockery::mock(ServiceManager::class);
        $services->shouldReceive('has')->andReturn(true)->byDefault();
        $services->shouldReceive('get')
            ->with('foobar')
            ->once()
            ->andReturn(new MeRule());

        $config     = ['foo-bar' => 'foobar'];
        $rules      = new RuleCollection($services, $config);
        $collection = new SuggestionCollection();

        $this->assertEmpty($rules->apply($collection, new Child()));
        $this->assertEmpty($rules->apply($collection, new Child()));
    }
}
