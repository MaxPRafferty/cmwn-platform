<?php

namespace IntegrationTest\Service;

use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Suggest\Service\SuggestedServiceInterface;
use Suggest\Suggestion;
use User\Child;
use User\UserInterface;
use Zend\Paginator\Paginator;

/**
 * Class SuggestedServiceTest
 *
 * @package IntegrationTest\Service
 * @group   Db
 * @group   IntegrationTest
 * @group   Friend
 * @group   Suggest
 * @group   SuggestService
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
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../DataSets/suggest.dataset.php');
    }

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
        $suggestions = new Paginator(
            $this->suggestedService
                ->fetchSuggestedFriendsForUser(new Child(['user_id' => 'english_student']))
        );

        $actualIds   = [];
        /** @var UserInterface $suggest */
        foreach ($suggestions as $suggest) {
            $this->assertInstanceOf(UserInterface::class, $suggest);
            array_push($actualIds, $suggest->getUserId());
        }

        $this->assertEquals(
            ['english_student_1', 'english_student_2'],
            $actualIds
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachSuggestion()
    {
        $this->assertTrue(
            $this->suggestedService->attachSuggestedFriendForUser(
                new Child(['user_id' => 'english_student']),
                new Child(['user_id' => 'math_student'])
            )
        );

        $suggestions = new Paginator(
            $this->suggestedService
                ->fetchSuggestedFriendsForUser(new Child(['user_id' => 'english_student']))
        );

        $actualIds   = [];
        $expectedIds = ['english_student_1', 'english_student_2', 'math_student'];

        /** @var UserInterface $suggest */
        foreach ($suggestions as $suggest) {
            $this->assertInstanceOf(UserInterface::class, $suggest);
            array_push($actualIds, $suggest->getUserId());
        }

        sort($actualIds);
        sort($expectedIds);
        $this->assertEquals(
            $expectedIds,
            $actualIds,
            'Math student was not attached as a suggestion to english student'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotAttachInverseSuggestion()
    {
        $this->assertFalse(
            $this->suggestedService->attachSuggestedFriendForUser(
                new Child(['user_id' => 'english_student_1']),
                new Child(['user_id' => 'english_student'])
            ),
            'attachSuggestedFriendForUser did not return false when adding inverse suggestion'
        );

        $pdo = $this->getDatabaseTester()->getConnection()->getConnection();
        $sql = 'SELECT * FROM user_suggestions WHERE user_id = "english_student_1" AND suggest_id = "english_student"';
        foreach ($pdo->query($sql) as $row) {
            $this->fail('The inverse suggestion was added');
        }

        $sql = 'SELECT * FROM user_suggestions WHERE user_id = "english_student" AND suggest_id = "english_student_1"';
        foreach ($pdo->query($sql) as $row) {
            return;
        }

        $this->fail('The current suggestion for english student was removed');
    }

    /**
     * @test
     */
    public function testItShouldDeleteSuggestion()
    {
        $this->assertTrue(
            $this->suggestedService->deleteSuggestionForUser(
                new Child(['user_id' => 'english_student']),
                new Child(['user_id' => 'english_student_1'])
            )
        );


        $pdo = $this->getDatabaseTester()->getConnection()->getConnection();
        $sql = 'SELECT * FROM user_suggestions WHERE user_id = "english_student" AND suggest_id = "english_student_1"';
        foreach ($pdo->query($sql) as $row) {
            $this->fail('The suggestion was not deleted');
        }

        $sql = 'SELECT * FROM user_suggestions WHERE user_id = "english_student_2" AND suggest_id = "english_student"';
        foreach ($pdo->query($sql) as $row) {
            return;
        }

        $this->fail('The suggestion for english_student to english_student_1 was also removed');
    }

    /**
     * @test
     */
    public function testItShouldDeleteInverseSuggestion()
    {
        $this->assertTrue(
            $this->suggestedService->deleteSuggestionForUser(
                new Child(['user_id' => 'english_student_1']),
                new Child(['user_id' => 'english_student'])
            )
        );


        $pdo = $this->getDatabaseTester()->getConnection()->getConnection();
        $sql = 'SELECT * FROM user_suggestions WHERE user_id = "english_student" AND suggest_id = "english_student_1"';
        foreach ($pdo->query($sql) as $row) {
            $this->fail('The suggestion was not deleted');
        }

        $sql = 'SELECT * FROM user_suggestions WHERE user_id = "english_student_2" AND suggest_id = "english_student"';
        foreach ($pdo->query($sql) as $row) {
            return;
        }

        $this->fail('The suggestion for english_student to english_student_1 was also removed');
    }
}
