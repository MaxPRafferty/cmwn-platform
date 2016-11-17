<?php

namespace Suggest;

use Friend\Service\FriendServiceInterface;
use IntegrationTest\AbstractDbTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Suggest\Engine\SuggestionEngine;
use Suggest\Service\SuggestedServiceInterface;
use User\Child;
use User\UserInterface;

/**
 * Class SuggestionEngineTest
 * @package SuggestTest\Engine
 * @group Db
 * @group IntegrationTest
 * @group Friend
 * @group Suggest
 * @group SuggestionEngine
 * @group SuggestService
 */
class SuggestionEngineTest extends AbstractDbTestCase
{
    /**
     * @var SuggestionEngine
     */
    protected $engine;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var SuggestedServiceInterface
     */
    protected $suggestedService;

    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @before
     */
    public function setUpEngine()
    {
        $this->engine = TestHelper::getDbServiceManager()->get(SuggestionEngine::class);
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
    public function setUpSuggestionService()
    {
        $this->suggestedService = TestHelper::getDbServiceManager()->get(SuggestedServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = TestHelper::getDbServiceManager()->get(FriendServiceInterface::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../DataSets/suggest.dataset.php');
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldProduceCorrectSuggestions()
    {
        $this->engine->setUser($this->user);
        $this->engine->perform();
        $suggestions = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['math_student', 'english_student_1', 'english_student_2'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        sort($actualIds);
        sort($expectedIds);
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * @test
     */
    public function testItShouldRemoveExistingSuggestionsForUser()
    {
        $this->suggestedService->attachSuggestedFriendForUser($this->user, new Child(['user_id' => 'math_student']));
        $suggestions = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['math_student', 'english_student_1', 'english_student_2'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds);

        $this->friendService->attachFriendToUser($this->user, new Child(['user_id' => 'math_student']));
        $this->engine->setUser($this->user);
        $this->engine->perform();

        $suggestions = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['english_student_1', 'english_student_2'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldNotAddPendingFriends()
    {
        $this->friendService->attachFriendToUser($this->user, new Child(['user_id' => 'math_student']));
        $this->engine->setUser($this->user);
        $this->engine->perform();

        $suggestions = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['english_student_1', 'english_student_2'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);

        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldNotAddExistingFriends()
    {
        $this->friendService->attachFriendToUser(new Child(['user_id' => 'math_student']), $this->user);
        $this->engine->setUser($this->user);
        $this->engine->perform();

        $suggestions = $this->suggestedService->fetchSuggestedFriendsForUser($this->user);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['english_student_1', 'english_student_2'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);

        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * @test
     * @ticket CORE-2669
     */
    public function testItShouldNotAddExistingFriendsInverse()
    {
        $mathStudent = new Child(['user_id' => 'math_student']);
        $this->friendService->attachFriendToUser($mathStudent, $this->user);
        $this->engine->setUser($mathStudent);
        $this->engine->perform();

        $suggestions = $this->suggestedService->fetchSuggestedFriendsForUser($mathStudent);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['english_student_1', 'english_student_2'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);

        $this->assertEquals($expectedIds, $actualIds);
    }
}
