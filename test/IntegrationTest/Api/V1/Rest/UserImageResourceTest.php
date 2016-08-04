<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;
use Asset\Service\UserImageServiceInterface;
use IntegrationTest\TestHelper;

/**
 * Test UserImageResourceTest
 *
 * @group User
 * @group Image
 * @group API
 * @group UserImage
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */

class UserImageResourceTest extends TestCase
{
    /**
     * @var UserImageServiceInterface
     */
    protected $userImageService;

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userImageService = TestHelper::getServiceManager()->get(UserImageServiceInterface::class);
    }

    /**
     * @test
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
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
     * @ticket CORE-839
     */
    public function testItShouldAllowNeighborsToSeeProfileImages()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('other_teacher');

        $this->dispatch('/user/other_principal/image');

        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertMatchedRouteName('api.rest.user-image');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('image_id', $body, 'Missing image_id from response body for user image');
        $this->assertEquals('profiles/dwtm7optf0qq62vcveef', $body['image_id'], 'Incorrect image_id returned for user');
    }

    /**
     * @test
     * @ticket CORE-894
     */
    public function testItShouldFetchPendingImage()
    {
        $this->markTestIncomplete("should be pending image but returns approved image");
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/user/english_student/image');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user-image');
        $this->assertControllerName('api\v1\rest\userimage\controller');
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('image_id', $body);
        $this->assertArrayHasKey('url', $body);
        $this->assertEquals('profiles/drkynjsedoegxb0hwvch', $body['image_id']);
        $this->assertEquals('https://res.cloudinary.com/changemyworldnow/image/upload/v1460592535/profiles/drkynjsedoegxb0hwvch.jpg', $body['url']);
    }

    /**
     * @test
     */
    public function testItShouldNotFetchImageIfUserNotLoggedIn()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/user/english_student/image');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('english_student');

        $this->dispatch('/user/english_student/image');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShould404IfImageNotPresent()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/user/math_student/image');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     */
    public function testItShouldCreateImage()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch(
            '/user/math_student/image',
            POST,
            ['image_id' => 'profiles/foo', 'url' => 'http://www.drodd.com/images14/Minions1.jpg']
        );
        $this->assertMatchedRouteName('api.rest.user-image');
        $this->assertControllerName('api\v1\rest\userimage\controller');
        $this->assertResponseStatusCode(201);
        $img = $this->userImageService->fetchImageForUser('math_student', false)->getArrayCopy();
        $this->assertArrayHasKey('image_id', $img);
        $this->assertEquals('profiles/foo', $img['image_id']);
        $this->assertArrayHasKey('url', $img);
        $this->assertEquals('http://www.drodd.com/images14/Minions1.jpg', $img['url']);
    }

    /**
     * @test
     */
    public function testItShould403CreateImage()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch(
            '/user/math_student/image',
            POST,
            ['image_id' => 'profiles/bar', 'url' => 'http://www.drodd.com/images14/Minions1.jpg']
        );
        $this->assertResponseStatusCode(403);
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'other_teacher',
                '/user/other_principal/image'
            ],
            1 => [
                'math_student',
                '/user/math_student/image',
                'POST',
                ['image_id' => 'profiles/foo', 'url' => 'http://www.drodd.com/images14/Minions1.jpg']
            ],
        ];
    }
}
