<?php

namespace SuggestTest\Engine;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Engine\SuggestionEngine;
use Suggest\Filter\FilterCollection;
use Suggest\Rule\RuleCollection;
use Suggest\Service\SuggestedServiceInterface;
use Suggest\SuggestionCollection;
use User\Child;
use User\Service\UserServiceInterface;

/**
 * Test SuggestionEngineTest
 *
 * @group Suggest
 * @group User
 * @group UserService
 * @group Friend
 * @group Rule
 * @group Filter
 * @group SuggestionEngine
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuggestionEngineTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|SuggestedServiceInterface
     */
    protected $suggestService;

    /**
     * @var \Mockery\MockInterface|RuleCollection
     */
    protected $ruleCollection;

    /**
     * @var \Mockery\MockInterface|FilterCollection
     */
    protected $filterCollection;

    /**
     * @var \Mockery\MockInterface|UserServiceInterface
     */
    protected $userService;

    /**
     * @var SuggestionEngine
     */
    protected $engine;

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->suggestService = \Mockery::mock(SuggestedServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpRuleCollection()
    {
        $this->ruleCollection = \Mockery::mock(RuleCollection::class);
    }

    /**
     * @before
     */
    public function setUpFilterCollection()
    {
        $this->filterCollection = \Mockery::mock(FilterCollection::class);
    }

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = \Mockery::mock(UserServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpEngine()
    {
        $this->engine = new SuggestionEngine(
            $this->ruleCollection,
            $this->filterCollection,
            $this->suggestService,
            $this->userService
        );
    }

    /**
     * @test
     */
    public function testItShouldExtractUserCorrectly()
    {
        $this->assertEquals(
            ['user_id' => null],
            $this->engine->getArrayCopy(),
            'Suggestion engine fails to copy array with no user'
        );

        $user = new Child(['user_id' => 'test_child']);
        $this->engine->setUser($user);
        $this->assertEquals(
            ['user_id' => 'test_child'],
            $this->engine->getArrayCopy(),
            'Suggestion engine fails to copy array with a user set'
        );
    }

    /**
     * @test
     */
    public function testItShouldHydrateWithUserId()
    {
        $user = new Child(['user_id' => 'test_child']);
        $this->userService->shouldReceive('fetchUser')
            ->with('test_child')
            ->once()
            ->andReturn($user);

        $this->engine->exchangeArray(['user_id' => 'test_child']);

        $this->assertEquals(
            $user,
            $this->engine->getUser(),
            'Engine did not hydrate correctly with a user id'
        );
    }

    /**
     * @test
     */
    public function testItShouldHydrateWithUser()
    {
        $user = new Child(['user_id' => 'test_child']);
        $this->userService->shouldNotReceive('fetchUser');

        $this->engine->exchangeArray(['user_id' => $user]);
        $this->assertEquals(
            $user,
            $this->engine->getUser(),
            'Engine did not hydrate correctly with a user'
        );
    }

    /**
     * @test
     */
    public function testItShouldFailToPerformWhenMissingUser()
    {
        $this->setExpectedException(
            \RuntimeException::class,
            'Missing user for suggestion engine'
        );

        $this->suggestService->shouldNotReceive('deleteAllSuggestionsForUser');
        $this->suggestService->shouldNotReceive('attachSuggestedFriendForUser');
        $this->filterCollection->shouldNotReceive('getSuggestions');
        $this->ruleCollection->shouldNotReceive('apply');

        $this->engine->perform();
    }

    /**
     * @test
     */
    public function testItShouldPerformWithNoUsersToAttach()
    {
        $user = new Child(['user_id' => 'test_child', 'username' => 'manchuck']);
        $this->engine->setUser($user);

        $this->suggestService->shouldReceive('deleteAllSuggestionsForUser')
            ->with($user)
            ->once();

        $this->filterCollection->shouldReceive('getSuggestions')
            ->once();

        $this->ruleCollection->shouldReceive('apply')
            ->once();

        $this->suggestService->shouldNotReceive('attachSuggestedFriendForUser');

        $this->assertEmpty($this->engine->perform());
    }

    /**
     * @test
     */
    public function testItShouldPerformWithOneUserToAttach()
    {
        $user   = new Child(['user_id' => 'test_child', 'username' => 'manchuck']);
        $friend = new Child(['user_id' => 'friend_1', 'username' => 'chuckman']);

        $this->engine->setUser($user);

        $this->suggestService->shouldReceive('deleteAllSuggestionsForUser')
            ->with($user)
            ->once();

        $this->filterCollection->shouldReceive('getSuggestions')
            ->once()
            ->andReturnUsing(function (SuggestionCollection $collection) use (&$friend) {
                $collection->append($friend);
            });

        $this->ruleCollection->shouldReceive('apply')
            ->once();

        $this->suggestService->shouldReceive('attachSuggestedFriendForUser')
            ->once()
            ->with($user, $friend);

        $this->assertEmpty($this->engine->perform());
    }

    /**
     * @test
     */
    public function testItShouldPerformWithMultipleUsersToAttach()
    {
        $user        = new Child(['user_id' => 'test_child', 'username' => 'manchuck']);
        $friendOne   = new Child(['user_id' => 'friend_1', 'username' => 'chuck_man_1']);
        $friendTwo   = new Child(['user_id' => 'friend_2', 'username' => 'chuck_man_2']);
        $friendThree = new Child(['user_id' => 'friend_3', 'username' => 'chuck_man_3']);

        $this->engine->setUser($user);

        $this->suggestService->shouldReceive('deleteAllSuggestionsForUser')
            ->with($user)
            ->once();

        $this->filterCollection->shouldReceive('getSuggestions')
            ->once()
            ->andReturnUsing(function (SuggestionCollection $collection) use (&$friendOne, &$friendTwo, &$friendThree) {
                $collection->append($friendOne);
                $collection->append($friendTwo);
                $collection->append($friendThree);
            });

        $this->ruleCollection->shouldReceive('apply')
            ->once();

        $this->suggestService->shouldReceive('attachSuggestedFriendForUser')
            ->atLeast(1)
            ->with($user, $friendOne)
            ->ordered();

        $this->suggestService->shouldReceive('attachSuggestedFriendForUser')
            ->atLeast(1)
            ->with($user, $friendTwo)
            ->ordered();

        $this->suggestService->shouldReceive('attachSuggestedFriendForUser')
            ->atLeast(1)
            ->with($user, $friendThree)
            ->ordered();

        $this->assertEmpty($this->engine->perform());
    }

    /**
     * @test
     */
    public function testItShouldPerformWithMultipleUsersToAttachAndNotExceed()
    {
        $user        = new Child(['user_id' => 'test_child', 'username' => 'manchuck']);
        $suggestions = [];
        for ($suggestedCount = 0; $suggestedCount < SuggestionEngine::MAX_CAPACITY + 5; $suggestedCount++) {
            array_push(
                $suggestions,
                new Child([
                    'user_id'  => 'friend_' . $suggestedCount,
                    'username' => 'manchuck_' . $suggestedCount,
                ])
            );
        }

        $this->assertGreaterThan(
            SuggestionEngine::MAX_CAPACITY,
            count($suggestions)
        );

        $this->engine->setUser($user);

        $this->suggestService->shouldReceive('deleteAllSuggestionsForUser')
            ->with($user)
            ->once();

        $this->filterCollection->shouldReceive('getSuggestions')
            ->once()
            ->andReturnUsing(function (SuggestionCollection $collection) use (&$suggestions) {
                foreach ($suggestions as $suggestedUser) {
                    $collection->append($suggestedUser);
                }

                $this->assertGreaterThan(
                    SuggestionEngine::MAX_CAPACITY,
                    $collection->count()
                );
            });

        $this->ruleCollection->shouldReceive('apply')
            ->once();

        $this->suggestService->shouldReceive('attachSuggestedFriendForUser')
            ->times(SuggestionEngine::MAX_CAPACITY);

        $this->assertEmpty($this->engine->perform());
    }
}
