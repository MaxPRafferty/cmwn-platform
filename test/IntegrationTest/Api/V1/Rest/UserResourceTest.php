<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Service\UserServiceInterface;
use User\StaticUserFactory;
use User\UserInterface;
use Zend\Json\Json;

/**
 * Test UserResourceTest
 *
 * @group User
 * @group IntegrationTest
 * @group Api
 * @group UserService
 * @group UserGroupService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserResourceTest extends TestCase
{
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
    public function testItShould404OnGetToNonExistentUser()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/user/foo_bar');
        $this->assertResponseStatusCode(404);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();
    }

    /**
     * @test
     */
    public function testItShould404OnPutToNonExistentUser()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/user/foo_bar', 'PUT', [], true);
        $this->assertResponseStatusCode(404);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();
    }

    /**
     * @test
     */
    public function testItShould401OnFetchWhenTryingTooAccessEnglishStudentWhenNotLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->assertFalse($this->getAuthService()->hasIdentity());
        $this->dispatch('/user/english_student');
        $this->assertResponseStatusCode(401);
        $this->assertCorrectCorsHeaders();
    }

    /**
     * @param $access
     * @param $login
     * @test
     * @dataProvider getAccessProvider
     */
    public function testItShouldReturnCorrectCodeOnFetchWhenTryingTooAccessUser($login, $access, $code)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/user/' . $access);

        $this->assertResponseStatusCode($code);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();
        $this->assertCorrectCorsHeaders();
    }

    /**
     * @test
     * @param $login
     * @param $expectedIds
     * @dataProvider getListAccessProvider
     */
    public function testItShouldReturnCorrectUsersWhenAccessingFetchAll($login, array $expectedIds)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/user');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();
        $this->assertCorrectCorsHeaders();

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('user', $embedded, 'Embedded does not contain any users');

        $actualIds = [];
        foreach ($embedded['user'] as $user) {
            $actualUser  = StaticUserFactory::createUser($user);
            $actualIds[] = $actualUser->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Users for: ' . $login);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordExceptionForPutMe()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $putData = [
            'first_name'  => 'Adam',
            'last_name'   => 'Welzer',
            'gender'      => 'Female',
            'meta'        => '[]',
            'type'        => 'ADULT',
            'username'    => 'new_username',
            'email'       => 'adam@ginasink.com',
            'birthdate'   => '1982-05-13',
        ];
        $this->dispatch('/user/english_student', 'PUT', $putData, true);
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     */
    public function testItShouldCorrectlyPutMeUser()
    {
        $beforeUser = $this->loadUserFromDb('english_teacher');
        $this->assertInstanceOf(UserInterface::class, $beforeUser);
        $this->assertEquals('english_teacher', $beforeUser->getUserName());
        $this->assertEquals('Angelot', $beforeUser->getFirstName());
        $this->assertEquals('Fredickson', $beforeUser->getLastName());
        $this->assertEquals('M', $beforeUser->getGender());

        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $putData = [
            'first_name'  => 'Adam',
            'last_name'   => 'Welzer',
            'gender'      => 'Female',
            'meta'        => '[]',
            'type'        => 'ADULT',
            'username'    => 'new_username',
            'email'       => 'adam@ginasink.com',
            'birthdate'   => '1982-05-13',
        ];

        $this->dispatch('/user/english_teacher', 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $afterUser = $this->loadUserFromDb('english_teacher');

        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertNotEquals($beforeUser, $afterUser);

        $this->assertEquals('new_username', $afterUser->getUserName());
        $this->assertEquals('Adam', $afterUser->getFirstName());
        $this->assertNull($afterUser->getMiddleName());
        $this->assertEquals('Welzer', $afterUser->getLastName());
        $this->assertEquals('Female', $afterUser->getGender());
        $this->assertEquals($beforeUser->getCreated(), $afterUser->getCreated());
    }

    /**
     * @test
     */
    public function testItShouldNotChangeUserTypeOnPut()
    {
        $beforeUser = $this->loadUserFromDb('english_teacher');
        $this->assertInstanceOf(UserInterface::class, $beforeUser);
        $this->assertEquals('english_teacher', $beforeUser->getUserName());

        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $putData = [
            'first_name'  => 'Angelot',
            'middle_name'  => 'M',
            'last_name'   => 'Fredickson',
            'gender'      => 'M',
            'meta'        => '[]',
            'type'        => 'CHILD',
            'username'    => 'english_teacher',
            'email'       => 'english_teacher@ginasink.com',
            'birthdate'   => '2016-04-15',
        ];

        $this->dispatch('/user/english_teacher', 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $afterUser = $this->loadUserFromDb('english_teacher');

        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertEquals(UserInterface::TYPE_ADULT, $afterUser->getType());
    }

    /**
     * @test
     * @ticket CORE-800
     */
    public function testItShouldAllowTeacherTooMakeChangesToStudent()
    {
        $beforeUser = $this->loadUserFromDb('english_student');
        $this->assertInstanceOf(UserInterface::class, $beforeUser);
        $this->assertEquals('english_student', $beforeUser->getUserName());

        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $putData = [
            'first_name'  => 'Adam',
            'last_name'   => 'Welzer',
            'gender'      => 'Female',
            'meta'        => '[]',
            'type'        => 'CHILD',
            'username'    => 'new_username',
            'email'       => 'adam@ginasink.com',
            'birthdate'   => '1982-05-13',
        ];

        $this->dispatch('/user/english_student', 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $afterUser = $this->loadUserFromDb('english_student');

        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertNotEquals($beforeUser, $afterUser);

        $this->assertEquals('english_student', $afterUser->getUserName());
        $this->assertEquals('Adam', $afterUser->getFirstName());
        $this->assertNull($afterUser->getMiddleName());
        $this->assertEquals('Welzer', $afterUser->getLastName());
        $this->assertEquals('Female', $afterUser->getGender());
        $this->assertEquals($beforeUser->getCreated(), $afterUser->getCreated());
    }

    /**
     * @test
     * @ticket CORE-800
     * @dataProvider loginDataProvider
     */
    public function testItShouldNotAllowOtherUsersToChangeUsernames($login)
    {
        $beforeUser = $this->loadUserFromDb('english_student');
        $this->assertInstanceOf(UserInterface::class, $beforeUser);
        $this->assertEquals('english_student', $beforeUser->getUserName());

        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $putData = [
            'first_name'  => 'Adam',
            'last_name'   => 'Welzer',
            'gender'      => 'Female',
            'meta'        => '[]',
            'type'        => 'CHILD',
            'username'    => 'new_username',
            'email'       => 'adam@ginasink.com',
            'birthdate'   => '1982-05-13',
        ];

        $this->dispatch('/user/english_student', 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $afterUser = $this->loadUserFromDb('english_student');

        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertNotEquals($beforeUser, $afterUser);

        $this->assertEquals('english_student', $afterUser->getUserName());
        $this->assertEquals('Adam', $afterUser->getFirstName());
        $this->assertNull($afterUser->getMiddleName());
        $this->assertEquals('Welzer', $afterUser->getLastName());
        $this->assertEquals('Female', $afterUser->getGender());
        $this->assertEquals($beforeUser->getCreated(), $afterUser->getCreated());
    }

    /**
     * @test
     * @ticket CORE-652
     */
    public function testItShouldReturnLatestImageForUserForMe()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }

        $this->assertArrayHasKey('_embedded', $decoded);
        $this->assertArrayHasKey('image', $decoded['_embedded']);
        $this->assertArrayHasKey('image_id', $decoded['_embedded']['image']);

        $this->assertEquals('english_pending', $decoded['_embedded']['image']['image_id']);
    }

    /**
     * @test
     * @ticket CORE-652
     */
    public function testItShouldReturnLastApprovedImageWhenOtherUserViewing()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');
        $this->dispatch('/user/english_student');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }

        $this->assertArrayHasKey('_embedded', $decoded);
        $this->assertArrayHasKey('image', $decoded['_embedded']);
        $this->assertArrayHasKey('image_id', $decoded['_embedded']['image']);

        $this->assertEquals('english_approved', $decoded['_embedded']['image']['image_id']);
    }

    /**
     * @test
     * @ticket CORE-727
     */
    public function testItShouldLetTeacherDeleteStudentProfile()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');
        $this->dispatch('/user/english_student', 'DELETE');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');

        $this->setExpectedException(NotFoundException::class);
        $this->loadUserFromDb('english_student');
    }

    /**
     * @param $userId
     * @return \User\UserInterface
     */
    protected function loadUserFromDb($userId)
    {
        /** @var UserServiceInterface $userService */
        $userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
        return $userService->fetchUser($userId);
    }

    /**
     * @return string[]
     */
    public function getAccessProvider()
    {
        return include __DIR__ . '/_providers/GET.access.provider.php';
    }

    /**
     * @return string[]
     */
    public function getListAccessProvider()
    {
        return include __DIR__ . '/_providers/GET.list.provider.php';
    }

    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            'Super User' => [
                'super_user'
            ],
            'Principal' => [
                'principal'
            ],
            'English Teacher' => [
                'english_teacher'
            ],
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
                '/user'
            ],
            1 => [
                'math_student',
                '/user/math_student',
            ],
        ];
    }
}
