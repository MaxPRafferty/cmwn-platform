<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use Zend\Json\Json;

/**
 * Class FeedResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class FeedResourceTest extends TestCase
{
    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/feed.dataset.php');
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
        $this->assertChangePasswordException('/feed', $method, $params);
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

    /**
     * @test
     */
    public function testItShouldFetchFeedById()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/feed/es_friend_feed');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.feed');
        $this->assertControllerName('api\v1\rest\feed\controller');

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
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFeed()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/feed');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.feed');
        $this->assertControllerName('api\v1\rest\feed\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey("_embedded", $body);
        $body = $body['_embedded'];
        $this->assertArrayHasKey('feed', $body);

        $feeds = $body['feed'];

        $expected = [
            'es_friend_feed',
            'ms_friend_feed',
            'es_game_feed',
            'ms_game_feed',
            'os_game_feed',
            'es_flip_feed',
            'ms_flip_feed',
            'os_flip_feed',
        ];
        $actual = [];

        foreach ($feeds as $body) {
            $this->assertArrayHasKey('feed_id', $body);
            $this->assertArrayHasKey('sender', $body);
            $this->assertArrayHasKey('title', $body);
            $this->assertArrayHasKey('message', $body);
            $this->assertArrayHasKey('priority', $body);
            $this->assertArrayHasKey('posted', $body);
            $this->assertArrayHasKey('visibility', $body);
            $this->assertArrayHasKey('type', $body);
            $this->assertArrayHasKey('type_version', $body);

            $actual[] = $body['feed_id'];
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider loginDataProvider
     */
    public function testItShouldNotAllowOthersToAccessFeed($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/feed');
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.feed');
        $this->assertControllerName('api\v1\rest\feed\controller');
    }

    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            'English Student' => ['english_student'],
            'English Teacher' => ['english_teacher'],
            'Principal' => ['principal'],
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
