<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Asset\Image;
use Asset\Service\UserImageServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Zend\Json\Json;

/**
 * Test ImageResourceTest
 *
 * @group Image
 * @group User
 * @group UserImageService
 * @group IntegrationTest
 * @group DB
 * @group ImageService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ImageResourceTest extends TestCase
{
    /**
     * @var UserImageServiceInterface
     */
    protected $imageService;

    /**
     * @before
     */
    public function setUpImageService()
    {
        $this->imageService = TestHelper::getServiceManager()->get(UserImageServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordUser()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch(
            '/user/english_student/image',
            'POST',
            ['image_id' => 'foobar', 'url' => 'www.example.com']
        );
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     * @dataProvider usersThatCanUploadImagesProvider
     */
    public function testItShouldAllowChildToUploadProfileImage($userId)
    {
        try {
            $this->imageService->fetchImageForUser($userId);
            $this->fail('The user has an image attached');
        } catch (NotFoundException $notFound) {
            // no op this is fine
        } catch (\Exception $unExpected) {
            $this->fail('Un-expected exception thrown: ' . $unExpected->getMessage());
        }

        $this->injectValidCsrfToken();
        $this->logInUser($userId);
        $this->dispatch(
            '/user/' . $userId . '/image',
            'POST',
            ['image_id' => 'foobar', 'url' => 'www.example.com']
        );

        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.user-image');
        $this->assertControllerName('api\v1\rest\userimage\controller');

        $image = $this->imageService->fetchImageForUser($userId, false);
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('foobar', $image->getImageId());
    }

    /**
     * @return array
     */
    public function usersThatCanUploadImagesProvider()
    {
        return [
            'Math Student'    => ['user_id' => 'math_student'],
            'English Teacher' => ['user_id' => 'english_teacher'],
            'Principal'       => ['user_id' => 'principal'],
            'Super'           => ['user_id' => 'super_user'],
        ];
    }
}
