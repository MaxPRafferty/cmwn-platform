<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use Zend\Json\Json;

/**
 * Class FeedUserResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class FeedUserResourceTest extends TestCase
{
    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/feed.dataset.php');
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $method
     * @param array $params
     *
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckChangePasswordException($user, $method = 'GET', $params = [])
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($user);
        $this->assertChangePasswordException('/user/'. $user .'/feed', $method, $params);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/feed');
        $this->assertResponseStatusCode(401);
    }

    public function testItShouldFetchFeedForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/feed/es_friend_feed');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.feed-user');
        $this->assertControllerName('api\v1\rest\feeduser\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('feed_id', $body);
        $this->assertArrayHasKey('sender', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('priority', $body);
        $this->assertArrayHasKey('posted', $body);
        $this->assertArrayHasKey('visibility', $body);
        $this->assertArrayHasKey('type', $body);
        $this->assertArrayHasKey('type_version', $body);
        $this->assertEquals('es_friend_feed', $body['feed_id']);
    }

    /**
     * @test
     * @dataProvider feedDataProvider
     */
    public function testItShouldFetchAllFeedForUserWithDescendingOrderOfPriority($login, $expectedFeed)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/user/' . $login .'/feed');
        $this->assertResponseStatusCode(200);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);

        $body = $body['_embedded'];
        $this->assertArrayHasKey('user-feed', $body);

        $userFeeds = $body['user-feed'];

        $actualFeed = [];

        foreach ($userFeeds as $body) {
            $this->assertArrayHasKey('feed_id', $body);
            $this->assertArrayHasKey('sender', $body);
            $this->assertArrayHasKey('title', $body);
            $this->assertArrayHasKey('message', $body);
            $this->assertArrayHasKey('priority', $body);
            $this->assertArrayHasKey('posted', $body);
            $this->assertArrayHasKey('visibility', $body);
            $this->assertArrayHasKey('type', $body);
            $this->assertArrayHasKey('type_version', $body);

            $actualFeed[] = $body['feed_id'];
        }

        $this->assertEquals($expectedFeed, $actualFeed);
    }

    /**
     * @return array
     */
    public function feedDataProvider()
    {
        return [
            [
                'english_student',
                ['game_feed', 'es_friend_feed', 'es_flip_feed'],
            ],
            [
                'math_student',
                ['game_feed', 'ms_friend_feed', 'ms_flip_feed'],
            ],
            [
                'other_student',
                ['game_feed', 'os_flip_feed'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
            ],
            'Principal' => [
                'principal',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
            'Super User' => [
                'super_user',
            ],
        ];
    }
}
