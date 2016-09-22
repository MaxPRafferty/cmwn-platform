<?php

namespace IntegrationTest\Api\V1\Rest;

use Friend\Service\FriendServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Suggest\Engine\SuggestionEngine;
use Zend\Json\Json;

/**
 * Test SuggestResourceTest
 *
 * @group Suggest
 * @group IntegrationTest
 * @group SuggestedService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SuggestResourceTest extends TestCase
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../../../DataSets/friends.dataset.php';

        return new ArrayDataSet($data);
    }

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->friendService = TestHelper::getServiceManager()->get(FriendServiceInterface::class);
        $this->suggestionEngine = TestHelper::getServiceManager()->get(SuggestionEngine::class);
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
     *
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckChangePasswordException($user, $url, $method = 'GET', $params = [])
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($user);
        $this->assertChangePasswordException($url, $method, $params);
    }

    /**
     * @test
     * @ticket CORE-558
     */
    public function testItShouldAllowChildToAccessSuggestEndpoint()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');
    }

    /**
     * @test
     * @ticket CORE-701
     * @ticket CORE-703
     */
    public function testItShouldReturnSuggestionsWhenUserHasPendingRequest()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->friendService->attachFriendToUser('math_student', 'english_student');
        $this->suggestionEngine->setUser('english_student');
        $this->suggestionEngine->perform();
        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('suggest', $body['_embedded']);
        $actualSuggestion = [];
        foreach ($body['_embedded']['suggest'] as $suggestData) {
            $actualSuggestion[] = [
                'user_id' => $suggestData['suggest_id'],
            ];
        }

        $expected = [
            [
                'user_id' => 'other_student',
            ],
        ];

        $this->assertEquals($expected, $actualSuggestion);
    }

    /**
     * @test
     * @ticket CORE-701
     * @ticket CORE-703
     */
    public function testItShouldReturnSuggestionsWhenUserHasNoFriends()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->suggestionEngine->setUser('english_student');
        $this->suggestionEngine->perform();
        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('suggest', $body['_embedded']);
        $actualSuggestion = [];
        foreach ($body['_embedded']['suggest'] as $suggestData) {
            $actualSuggestion[] = [
                'user_id' => $suggestData['suggest_id'],
            ];
        }

        $expected = [
            [
                'user_id' => 'math_student',
            ],
            [
                'user_id' => 'other_student',
            ],
        ];

        $this->assertEquals($expected, $actualSuggestion);
    }

    /**
     * @test
     * @ticket CORE-701
     * @ticket CORE-703
     */
    public function testItShouldReturnSuggestionsWhenUserHasRequestWaiting()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');
        $this->friendService->attachFriendToUser('english_student', 'math_student');
        $this->suggestionEngine->setUser('math_student');
        $this->suggestionEngine->perform();
        $this->dispatch('/user/math_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('suggest', $body['_embedded']);
        $actualSuggestion = [];
        foreach ($body['_embedded']['suggest'] as $suggestData) {
            $actualSuggestion[] = [
                'user_id' => $suggestData['suggest_id'],
            ];
        }

        $expected = [
            [
                'user_id' => 'other_student',
            ],
        ];

        $this->assertEquals($expected, $actualSuggestion);
    }

    /**
     * @test
     */
    public function testItShouldDeleteSuggestionWhenUserIsFriended()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->suggestionEngine->setUser('english_student');
        $this->suggestionEngine->perform();

        $this->friendService->attachFriendToUser('english_student', 'math_student');

        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('suggest', $body['_embedded']);
        $actualSuggestion = [];
        foreach ($body['_embedded']['suggest'] as $suggestData) {
            $actualSuggestion[] = [
                'user_id' => $suggestData['suggest_id'],
            ];
        }

        $expected = [
            [
                'user_id' => 'other_student',
            ],
        ];

        $this->assertEquals($expected, $actualSuggestion);
    }
    
    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/user/english_student/suggest',
            ],
        ];
    }
}
