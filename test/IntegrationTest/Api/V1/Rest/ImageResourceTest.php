<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Asset\Image;
use Asset\Service\UserImageServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use IntegrationTest\DataSets\ArrayDataSet;

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
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/image.dataset.php');
    }


    /**
     * @before
     */
    public function setUpImageService()
    {
        $this->imageService = TestHelper::getServiceManager()->get(UserImageServiceInterface::class);
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
        $this->assertEquals(0, $image->getModerationStatus());
    }

    /**
     * @return array
     */
    public function usersThatCanUploadImagesProvider()
    {
        return [
            'Math Student'    => ['user_id' => 'math_student'],
            'English Teacher' => ['user_id' => 'english_teacher'],
            'Super'           => ['user_id' => 'super_user'],
        ];
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/user/english_student/image',
                'POST',
                [
                    'image_id' => 'foobar',
                    'url' => 'www.example.com'
                ]
            ],
        ];
    }
}
