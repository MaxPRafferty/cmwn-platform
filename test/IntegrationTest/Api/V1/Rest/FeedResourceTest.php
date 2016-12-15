<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

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
        $this->assertChangePasswordException('/user/' . $user. '/feed', $method, $params);
    }

    /**
     * @test
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckIfUserIsLoggedIn($login)
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/user/' . $login . '/feed');
        $this->assertResponseStatusCode(401);
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
            $this->assertEquals('game', $feed['type']);
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
            $this->assertArrayHasKey('_links', $feed);
            $this->assertArrayHasKey('games', $feed['_links']);
        }
    }

    /**
     * @test
     * TODO This test will be used when more types of feed are added
     */
    public function testItShouldCheckIfImageIsBeingSentCorrectly()
    {
        $this->markTestIncomplete("To be done once more types of feed are added");
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/feed');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('feed', $body['_embedded']);

        $feeds = $body['_embedded']['feed'];

        foreach ($feeds as $feed) {
            if ($feed['type']==='game') {
                continue;
            }
            $this->assertArrayHasKey('sender', $feed);
            $this->assertArrayHasKey('image', $feed['sender']);
        }
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
            'Super Admin' => ['super_user'],
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
        ];
    }
}
