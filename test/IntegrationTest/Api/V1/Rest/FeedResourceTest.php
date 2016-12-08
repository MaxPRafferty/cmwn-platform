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
        $this->assertChangePasswordException('/feed', $method, $params);
    }

    /**
     * @test
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckIfUserIsLoggedIn($login)
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
        $this->dispatch('/feed/friend_feed');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.feed');
        $this->assertControllerName('api\v1\rest\feed\controller');
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFeed()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/feed');
        $this->assertResponseStatusCode(201);
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
