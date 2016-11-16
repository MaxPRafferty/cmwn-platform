<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Service\UserServiceInterface;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

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
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/user-name.dataset.php');
    }

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = TestHelper::getDbServiceManager()->get(UserServiceInterface::class);
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

        $this->dispatch('/user-name', 'POST', ['user_name' => 'active-albatross']);

        $this->assertResponseStatusCode(201);
        $this->assertControllerName('api\v1\rest\username\controller');

        $changedUser = $this->userService->fetchUser('english_student');
        $this->assertRegExp(
            '/^active-albatross\d{3}$/',
            $changedUser->getUserName(),
            'User Name was not appended with numbers when the user selected a name'
        );
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/user-name'
            ],
            1 => [
                'english_student',
                '/user-name',
                'POST',
                ['user_name' => 'active-albatross']
            ],
        ];
    }
}
