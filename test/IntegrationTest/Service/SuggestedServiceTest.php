<?php

namespace IntegrationTest\Service;

use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\TestHelper;
use Suggest\Service\SuggestedServiceInterface;
use Suggest\Suggestion;
use User\Child;
use User\UserInterface;
use Zend\Paginator\Paginator;

/**
 * Class SuggestedServiceTest
 * @package IntegrationTest\Service
 * @group Db
 * @group IntegrationTest
 * @group Friend
 * @group Suggest
 * @group SuggestService
 */
class SuggestedServiceTest extends TestCase
{
    /**
     * @var SuggestedServiceInterface
     */
    protected $suggestedService;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Suggestion
     */
    protected $suggestion;

    /**
     * @before
     */
    public function setUpSuggestedService()
    {
        $this->suggestedService = TestHelper::getDbServiceManager()->get(SuggestedServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Child(['user_id' => 'english_student']);
    }

    /**
     * @before
     */
    public function setUpSuggestion()
    {
        $this->suggestion = new Suggestion(['user_id' => 'math_student']);
    }

    /**
     * @test
     */
    public function testItShouldFetchSuggestions()
    {
        $this->suggestedService->attachSuggestedFriendForUser($this->user, $this->suggestion);
        $this->suggestedService->attachSuggestedFriendForUser(
            new Suggestion(['user_id' => 'other_student']),
            $this->user
        );

        $suggestions = new Paginator($this->suggestedService->fetchSuggestedFriendsForUser($this->user));
        $actualIds = [];
        /** @var UserInterface $suggest */
        foreach ($suggestions as $suggest) {
            $this->assertInstanceOf(UserInterface::class, $suggest);
            array_push($actualIds, $suggest->getUserId());
        }

        $this->assertEquals(
            ['math_student', 'other_student'],
            $actualIds
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachSuggestion()
    {
        $rowSet = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $this->assertEquals(count($rowSet), 0);
        $this->assertTrue($this->suggestedService->attachSuggestedFriendForUser($this->user, $this->suggestion));
        $rowSet = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $this->assertEquals(count($rowSet), 1);
    }

    /**
     * @test
     */
    public function testItShouldNotAttachIfSuggestionAlreadyExists()
    {
        $this->assertTrue($this->suggestedService->attachSuggestedFriendForUser($this->user, $this->suggestion));
        $rowSet = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $this->assertEquals(count($rowSet), 1);
        $this->assertFalse($this->suggestedService->attachSuggestedFriendForUser($this->user, $this->suggestion));
    }

    /**
     * @test
     */
    public function testItShouldDeleteSuggestions()
    {
        $this->suggestedService->attachSuggestedFriendForUser($this->user, $this->suggestion);
        $this->assertTrue($this->suggestedService->deleteSuggestionForUser($this->user, $this->suggestion));
        $rowSet = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $this->assertEquals(count($rowSet), 0);
    }
}
