<?php

namespace IntegrationTest\Api;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Service\UserServiceInterface;
use Zend\Json\Json;

/**
 * Test UserNameResourceTest
 *
 * @group Api
 * @group User
 * @group Db
 * @group IntegrationTest
 * @group Child
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserNameResourceTest extends TestCase
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/user-name');
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordExceptionForPost()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/user-name', 'POST', ['user_name' => 'active-alligator']);
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     */
    public function testItShouldGenerateRandomName()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();

        $this->dispatch('/user-name');

        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertControllerName('api\v1\rest\username\controller');

        $response = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('user_name', $response);
        $this->assertNotEmpty($response['user_name']);
        
        $this->assertRegExp(
            '/^[a-z]+-[a-z]+$/',
            $response['user_name'],
            'Response MUST NOT have numbers when choosing the name to have'
        );
    }

    /**
     * @test
     */
    public function testItShouldChangeTheUserNameAndAddNumbers()
    {
        $sanityUser = $this->userService->fetchUser('english_student');
        $this->assertEquals('english_student', $sanityUser->getUserName());

        $this->logInUser('english_student');
        $this->injectValidCsrfToken();

        $this->dispatch('/user-name', 'POST', ['user_name' => 'active-alligator']);

        $this->assertResponseStatusCode(201);
        $this->assertControllerName('api\v1\rest\username\controller');

        $changedUser = $this->userService->fetchUser('english_student');
        $this->assertRegExp(
            '/^active-alligator\d{3}$/',
            $changedUser->getUserName(),
            'User Name was not appended with numbers when the user selected a name'
        );
    }
}
