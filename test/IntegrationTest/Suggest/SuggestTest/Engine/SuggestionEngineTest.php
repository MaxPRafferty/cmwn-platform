<?php

namespace SuggestTest\Engine;

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
    protected $service;

    /**
     * @before
     */
    public function setUpEngine()
    {
        $this->engine = TestHelper::getServiceManager()->get(SuggestionEngine::class);
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
    public function setUpService()
    {
        $this->service = TestHelper::getServiceManager()->get(SuggestedServiceInterface::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../../../DataSets/friends.dataset.php';
        return new ArrayDataSet($data);
    }

    /**
     * @test
     */
    public function testItShouldAttachSuggestions()
    {
        $this->logInUser('english_student');
        $this->engine->setUser($this->user);
        $this->engine->perform();
        $suggestions = $this->service->fetchSuggestedFriendsForUser($this->user);
        $suggestions = $suggestions->getItems(0, 100);

        $expectedIds = ['math_student', 'other_student'];
        $actualIds = [];
        foreach ($suggestions as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }

        $this->assertEquals($expectedIds, $actualIds);
    }
}
