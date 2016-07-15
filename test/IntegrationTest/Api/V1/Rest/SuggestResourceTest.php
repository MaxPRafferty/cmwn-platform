<?php

namespace IntegrationTest\Api\V1\Rest;

use Friend\Service\FriendServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Zend\Json\Json;

/**
 * Test FriendResourceTest
 *
 * @group Friend
 * @group IntegrationTest
 * @group FriendService
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
    public function setUpFriendService()
    {
        $this->friendService = TestHelper::getServiceManager()->get(FriendServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
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
                'status'  => $suggestData['friend_status'],
            ];
        }

        $expected = [
            [
                'user_id' => 'other_student',
                'status'  => 'CAN_FRIEND',
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
                'status'  => $suggestData['friend_status'],
            ];
        }

        $expected = [
            [
                'user_id' => 'other_student',
                'status'  => 'CAN_FRIEND',
            ],
            [
                'user_id' => 'math_student',
                'status'  => 'CAN_FRIEND',
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
                'status'  => $suggestData['friend_status'],
            ];
        }

        $expected = [
            [
                'user_id' => 'other_student',
                'status'  => 'CAN_FRIEND',
            ],
            [
                'user_id' => 'english_student',
                'status'  => 'NEEDS_YOUR_ACCEPTANCE'
            ],
        ];

        $this->assertEquals($expected, $actualSuggestion);
    }
}
