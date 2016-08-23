<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * Class FeedResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class FeedResourceTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCheckIfUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/feed');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     * @param $login
     * @dataProvider loginDataProvider
     */
    public function testItShouldAccessFeedEndPoint($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/user/' .$login. '/feed');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function testItShouldHaveSenderNullIfFeedIsGame()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/feed');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('feed', $body['_embedded']);

        $feeds = $body['_embedded']['feed'];

        foreach ($feeds as $feed) {
            if ($feed['type']!=='game') {
                continue;
            }
            $this->assertEquals(null, $feed['sender']);
        }
    }

    /**
     * @test
     */
    public function testItShouldAddGameLinkToTheFeed()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/feed');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('feed', $body['_embedded']);

        $feeds = $body['_embedded']['feed'];

        foreach ($feeds as $feed) {
            if ($feed['type']!=='game') {
                continue;
            }
            $this->assertArrayHasKey('_links', $feed);
            $this->assertArrayHasKey('games', $feed['_links']);
        }
    }
    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            ['english_student'],
            ['english_teacher'],
            ['principal'],
            ['super_user'],
        ];
    }
}
