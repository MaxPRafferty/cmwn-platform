<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Child;
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
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/users.dataset.php');
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
     *
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
    }

    /**
     * @param $access
     * @param $login
     *
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
    }

    /**
     * @test
     *
     * @param $login
     * @param $expectedIds
     *
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
     * @ticket CORE-1164
     */
    public function testItShouldCheckChangePasswordExceptionForPutMe()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $putData = [
            'first_name' => 'Adam',
            'last_name'  => 'Welzer',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'ADULT',
            'username'   => 'new_username',
            'email'      => 'adam@ginasink.com',
            'birthdate'  => '1982-05-13',
        ];
        $this->dispatch('/user/english_student', 'PUT', $putData, true);
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     * @ticket CORE-2390
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
            'first_name' => 'Adam',
            'last_name'  => 'Welzer',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'ADULT',
            'username'   => 'new_username',
            'email'      => 'adam@ginasink.com',
            'birthdate'  => '1982-05-13',
        ];

        $this->dispatch('/user/english_teacher', 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $conn  = $this->getConnection()->getConnection();
        $query = "select normalized_username from users where username = 'new_username'";
        $stmt  = $conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll();
        $this->assertEquals('newusername', $row[0]['normalized_username']);

        $afterUser = $this->loadUserFromDb('english_teacher');
        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertNotEquals($beforeUser, $afterUser);

        $this->assertEquals('new_username', $afterUser->getUserName());
        $this->assertEquals('Adam', $afterUser->getFirstName());
        $this->assertNull($afterUser->getMiddleName());
        $this->assertEquals('Welzer', $afterUser->getLastName());
        $this->assertEquals('Female', $afterUser->getGender());
        $this->assertEquals($beforeUser->getCreated(), $afterUser->getCreated());
        $this->assertEquals('adam@ginasink.com', $afterUser->getEmail());
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
            'middle_name' => 'M',
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
     * @ticket CORE-2390
     */
    public function testItShouldAllowTeacherTooMakeChangesToStudent()
    {
        $beforeUser = $this->loadUserFromDb('english_student');
        $this->assertInstanceOf(UserInterface::class, $beforeUser);
        $this->assertEquals('english_student', $beforeUser->getUserName());

        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $putData = [
            'first_name' => 'Adam',
            'last_name'  => 'Welzer',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'CHILD',
            'username'   => 'new_username',
            'email'      => 'adam@ginasink.com',
            'birthdate'  => '1982-05-13',
        ];

        $this->dispatch('/user/english_student', 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $afterUser = $this->loadUserFromDb('english_student');

        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertNotEquals($beforeUser, $afterUser);

        $this->assertEquals($beforeUser->getUserName(), $afterUser->getUserName());
        $this->assertEquals('Adam', $afterUser->getFirstName());
        $this->assertNull($afterUser->getMiddleName());
        $this->assertEquals('Welzer', $afterUser->getLastName());
        $this->assertEquals('Female', $afterUser->getGender());
        $this->assertEquals($beforeUser->getCreated(), $afterUser->getCreated());
        $this->assertEquals($beforeUser->getEmail(), $afterUser->getEmail());
    }

    /**
     * @test
     * @ticket       CORE-800
     * @ticket       CORE-2390
     * @dataProvider updateDataProvider
     */
    public function testItShouldAllowSuperToMakeChangesToUsers($login)
    {
        $beforeUser = $this->loadUserFromDb($login);
        $this->assertInstanceOf(UserInterface::class, $beforeUser);
        $this->assertEquals($login, $beforeUser->getUserName());

        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $putData = [
            'first_name' => 'Adam',
            'last_name'  => 'Walzer',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'CHILD',
            'username'   => 'new_username',
            'email'      => 'adam@ginasink.com',
            'birthdate'  => '1982-05-13',
        ];

        $this->dispatch('/user/' . $login, 'PUT', $putData, true);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();

        $afterUser = $this->loadUserFromDb($login);

        $this->assertInstanceOf(UserInterface::class, $afterUser);
        $this->assertNotEquals($beforeUser, $afterUser);

        $this->assertEquals('new_username', $afterUser->getUserName());
        $this->assertEquals('Adam', $afterUser->getFirstName());
        $this->assertNull($afterUser->getMiddleName());
        $this->assertEquals('Walzer', $afterUser->getLastName());
        $this->assertEquals('Female', $afterUser->getGender());
        $this->assertEquals($beforeUser->getCreated(), $afterUser->getCreated());
        // FIXME Children are not allowed to change their email address
//        $this->assertEquals('adam@ginasink.com', $afterUser->getEmail());
        $this->assertEquals($beforeUser->getType(), $afterUser->getType());
    }

    /**
     * @test
     * @ticket       CORE-800
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
            'first_name' => 'Adam',
            'last_name'  => 'Welzer',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'CHILD',
            'username'   => 'new_username',
            'email'      => 'adam@ginasink.com',
            'birthdate'  => '1982-05-13',
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

        $this->assertResponseStatusCode(204);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');

        $this->expectException(NotFoundException::class);
        $this->loadUserFromDb('english_student');
    }

    /**
     * @test
     * @ticket CORE-1164
     */
    public function testItShouldCreateUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'first_name' => 'Chaithra',
            'last_name'  => 'Yenikapati',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'CHILD',
            'username'   => 'wigglytuff-007',
            'email'      => 'chaithra@ginasink.com',
            'birthdate'  => '1993-07-13',
        ];
        $this->dispatch('/user', 'POST', $postData);
        $this->assertResponseStatusCode(201);

        $conn  = $this->getConnection()->getConnection();
        $query = "select normalized_username from users where username = 'wigglytuff-007'";
        $stmt  = $conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetchAll();
        $this->assertEquals('wigglytuff007', $row[0]['normalized_username']);
    }

    /**
     * Test if username already exists while create
     *
     * @test
     */
    public function testItShouldCheckIfUsernameIsDuplicateOnPost()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'first_name' => 'Chaithra',
            'last_name'  => 'Yenikapati',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'CHILD',
            'username'   => 'english_student',
            'email'      => 'chaithra@ginasink.com',
            'birthdate'  => '1993-07-13',
        ];
        $this->dispatch('/user', 'POST', $postData);
        $this->assertResponseStatusCode(422);
    }

    /**
     * Test if username already exists while update
     *
     * @test
     */
    public function testItShouldCheckIfUsernameIsDuplicateOnPut()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $putData = [
            'first_name'  => 'Angelot',
            'middle_name' => 'M',
            'last_name'   => 'Fredickson',
            'gender'      => 'M',
            'meta'        => '[]',
            'type'        => 'CHILD',
            'username'    => 'english_student',
            'email'       => 'english_teacher@ginasink.com',
            'birthdate'   => '2016-04-15',
        ];

        $this->dispatch('/user/english_teacher', 'PUT', $putData, true);
        $this->assertResponseStatusCode(422);
    }

    /** test
     *
     * @ticket CORE-2331
     * @group  MissingApiRoute
     */
    public function testItShouldCreateUserWithUserData()
    {
        $this->markTestIncomplete("Allow user resource to post from this end point with the following values set");

        $date     = new \DateTime();
        $postData = [
            'email'       => 'child@changemyworldnow.com',
            'first_name'  => 'foo',
            'middle_name' => 'bar',
            'last_name'   => 'trump',
            'gender'      => 'male',
            'birthdate'   => $date->format("Y-m-d H:i:s"),
            'type'        => Child::TYPE_CHILD,
            'meta'        => [],
            'external_id' => null,
        ];

        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user', 'POST', $postData);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfEmailIsDuplicateOnPost()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'first_name' => 'Chaithra',
            'last_name'  => 'Yenikapati',
            'gender'     => 'Female',
            'meta'       => '[]',
            'type'       => 'CHILD',
            'username'   => 'wigglytuff-007',
            'email'      => 'english_student@ginasink.com',
            'birthdate'  => '1993-07-13',
        ];
        $this->dispatch('/user', 'POST', $postData);
        $this->assertResponseStatusCode(422);
    }

    /** test
     *
     * @ticket CORE-2331
     * @group  MissingApiRoute
     */
    public function testItShouldFetchUserByExternalId()
    {
        $this->markTestIncomplete("Allow user resource to fetch by external id by setting a route param externalId");
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user?externalId=foo');
    }

    /**
     * @test
     */
    public function testItShouldCheckIfEmailIsDuplicateOnPut()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $putData = [
            'first_name'  => 'Angelot',
            'middle_name' => 'M',
            'last_name'   => 'Fredickson',
            'gender'      => 'M',
            'meta'        => '[]',
            'type'        => 'ADULT',
            'username'    => 'english_teacher',
            'email'       => 'english_student@ginasink.com',
            'birthdate'   => '2016-04-15',
        ];

        $this->dispatch('/user/english_teacher', 'PUT', $putData, true);
        $this->assertResponseStatusCode(422);

        $body = $this->getResponse()->getContent();
        $body = Json::decode($body, Json::TYPE_ARRAY);
        $this->assertArrayHasKey('validation_messages', $body);
        $this->assertArrayHasKey('email', $body['validation_messages']);
        $emailMessage = $body['validation_messages']['email'];
        $this->assertEquals('Invalid Email', $emailMessage[0]);
    }

    /** test
     *
     * @ticket CORE-2331
     * @group  MissingApiRoute
     */
    public function testItShouldFetchUserByEmail()
    {
        $this->markTestIncomplete("Allow user resource to fetch user by email by setting a route param email");
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user?email=foo@ginasink.com');
    }

    /**
     * test
     *
     * @ticket CORE-2746
     */
    public function testItShouldFetchUsersByType()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user?type=CHILD');
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('_links', $body);
        $this->assertArrayHasKey('total_items', $body);
    }

    /**
     * @test
     * @dataProvider halLinkDataProvider
     * @group        Hal
     */
    public function testItShouldCorrectlyAddHalLinksOnFetch($login, $user, $expected)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/user/' . $user);
        $this->assertResponseStatusCode(200);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_links', $body);
        $links = $body['_links'];

        $actual = [];
        foreach ($links as $label => $link) {
            $actual[] = $label;
        }

        sort($actual);
        sort($expected);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShouldLoadGroupsForPageTwoAndBuildCorrectFindLink()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user?page=2&per_page=1');
        $this->assertResponseStatusCode(200);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_links', $body);
        $links = $body['_links'] ?? [];

        $this->assertArrayHasKey('find', $links);

        $this->assertEquals(
            ['href' => 'http://api.test.com/user?per_page=1{&page}', 'templated' => true],
            $links['find'],
            'Find link was incorrectly built for user endpoint'
        );
    }

    /**
     * @param $userId
     *
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
            'Principal'       => [
                'principal',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
        ];
    }

    /**
     * @return array
     */
    public function updateDataProvider()
    {
        return [
            'Principal'       => [
                'principal',
            ],
            'English Student' => [
                'english_student',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
            'other_student'   => [
                'other_student',
            ],
            'Other Teacher'   => [
                'other_teacher',
            ],
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
                '/user',
            ],
            'Math Student'    => [
                'math_student',
                '/user/math_student',
            ],
        ];
    }

    /**
     * @return array
     */
    public function halLinkDataProvider()
    {
        return [
            'Super to principal'              => [
                'super_user',
                'principal',
                [
                    'self',
                    'profile',
                    'user_image',
                    'super',
                    'group_class',
                    'group_school',
                    'user_flip',
                    'org_district',
                ],
            ],
            'Super to english teacher'        => [
                'super_user',
                'english_teacher',
                [
                    'self',
                    'profile',
                    'user_image',
                    'super',
                    'group_class',
                    'group_school',
                    'user_flip',
                    'org_district',
                ],
            ],
            'Super to english student'        => [
                'super_user',
                'english_student',
                [
                    'self',
                    'profile',
                    'user_image',
                    'user_flip',
                    'reset',
                    'group_class',
                ],
            ],
            'principal to student'            => [
                'principal',
                'english_student',
                [
                    'self',
                    'profile',
                    'user_image',
                    'user_flip',
                    'reset',
                    'group_class',
                ],
            ],
            'principal to teacher'            => [
                'principal',
                'english_teacher',
                [
                    'self',
                    'profile',
                    'user_image',
                    'user_flip',
                    'group_class',
                    'group_school',
                    'org_district',
                ],
            ],
            'english teacher to student'      => [
                'english_teacher',
                'english_student',
                [
                    'self',
                    'profile',
                    'user_image',
                    'user_flip',
                    'reset',
                    'group_class',
                ],
            ],
            'math student to english student' => [
                'math_student',
                'english_student',
                [
                    'self',
                    'profile',
                    'user_image',
                    'user_flip',
                    'friend',
                    'group_class',
                ],
            ],
        ];
    }
}
