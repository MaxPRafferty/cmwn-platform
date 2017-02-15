<?php

namespace IntegrationTest\Api\V1\Rest;

use Group\Service\UserGroupServiceInterface;
use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\TestHelper;
use Security\Service\SecurityGroupServiceInterface;
use User\Adult;
use User\Child;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Test GroupUsersResourceTest
 *
 * @group DB
 * @group UserGroup
 * @group IntegrationTest
 * @group Api
 * @group Group
 * @group User
 * @SuppressWarnings(PHPMD)
 */
class GroupUsersResourceTest extends AbstractApigilityTestCase
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var SecurityGroupServiceInterface
     */
    protected $securityGroupService;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->userGroupService = TestHelper::getDbServiceManager()->get(UserGroupServiceInterface::class);
        $this->securityGroupService = TestHelper::getDbServiceManager()->get(SecurityGroupServiceInterface::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/group.dataset.php');
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
    public function testItShouldCheckLogin()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/group/math/user');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfValidGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/group/foo/user');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     *
     * @param $user
     * @param $group
     * @param $expectedIds
     *
     * @ticket       CORE-1059
     * @dataProvider userDataProvider
     */
    public function testItShouldReturnUsersAGroupForAdults($user, $group, $expectedIds)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);

        $this->dispatch('/group/' . $group . '/user');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('items', $body['_embedded']);
        $groupUsers = $body['_embedded']['items'];
        $actualIds  = [];
        foreach ($groupUsers as $user) {
            $this->assertArrayHasKey('user_id', $user);
            $actualIds[] = $user['user_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }

    /**
     * @test
     *
     * @param $user
     * @param $group
     *
     * @ticket       CORE-1184
     * @ticket       CORE-2191
     * @dataProvider childDataProvider
     */
    public function testItShouldReturnGroupForChild($user, $group)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);

        $this->dispatch('/group/' . $group . '/user');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function testItShouldNotReturnUsersOfOtherGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/group/english/user');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(403);
    }
    
    /**
     * @test
     * @dataProvider deleteDataProvider
     */
    public function testItShouldDetachUserFromAGroup($login, $url, $user)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch($url . $user->getUserId(), 'DELETE');
        $this->assertResponseStatusCode(204);
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');

        $groups = $this->userGroupService->fetchGroupsForUser($user);
        $groups = $groups->getItems(0, $groups->count());
        $actual = [];
        foreach ($groups as $group) {
            $group = $group->getArrayCopy();
            $actual[] = $group['group_id'];
        }
        $this->assertEquals([], $actual);
    }

    /**
     * @test
     * @dataProvider invalidDeleteDataProvider
     * @param $login
     * @param $url
     */
    public function testItShouldNotDeleteUserInAGroupWithInvalidAccess($login, $url)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch($url, 'DELETE');
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
    }

    /**
     * test
     * @param $role
     * @param $actualRole
     * @ticket CORE-2331
     * @group MissingApiRoute
     * @dataProvider postDataProvider
     */
    public function testItShouldAttachUserToGroup(
        $login,
        $group,
        $user,
        $role,
        $actualRole
    ) {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $postData = [
            'role' => $role,
            'user_id' => $user->getUserId(),
        ];

        $this->dispatch('/group/' . $group. '/user/' . $user->getUserId(), 'POST', $postData);
        $this->assertResponseStatusCode(201);

        $role = $this->securityGroupService->getRoleForGroup($group, $user);
        $this->assertEquals($actualRole, $role);
    }

    /**
     * @test
     * @param $login
     * @param $url
     * @param $role
     * @param $code
     * @dataProvider invalidPostDataProvider
     */
    public function testItShouldNotAttachUserTOGroupWithInaccessibleUsersAndGroups($login, $url, $role, $userId, $code)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch($url, 'POST', ['role' => $role, 'user_id' => $userId]);
        $this->assertResponseStatusCode($code);
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            [
                'english_student',
                '/group/school/user',
            ],
        ];
    }

    /**
     * @return array
     */
    public function userDataProvider()
    {
        return [
            'math teacher' => [
                'math_teacher',
                'math',
                ['math_student'],
            ],
            'english teacher' => [
                'english_teacher',
                'english',
                ['english_student'],
            ],
            'principal'    => [
                'principal',
                'school',
                ['english_teacher', 'english_student', 'math_teacher', 'math_student'],
            ],
        ];
    }


    /**
     * @return array
     */
    public function childDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
                'english',
            ],
            'Math Student' => [
                'math_student',
                'math',
            ],
        ];
    }

    /**
     * @return array
     */
    public function deleteDataProvider()
    {
        return [
            [
                'super_user',
                '/group/school/user/',
                new Adult(['user_id' => 'principal']),
            ],
            [
                'principal',
                '/group/english/user/',
                new Adult(['user_id' => 'english_teacher']),
            ],
            [
                'principal',
                '/group/english/user/',
                new Child(['user_id' => 'english_student']),
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidDeleteDataProvider()
    {
        return [
            [
                'english_teacher',
                '/group/english/user/english_student',
            ],
            [
                'principal',
                '/group/other_math/user/other_student',
            ],
            [
                'english_teacher',
                '/group/english/user/english_student',
            ],
        ];
    }

    /**
     * @return array
     */
    public function postDataProvider()
    {
        return [
            [
                'super_user',
                'other_math',
                new Child(['user_id' => 'english_student']),
                'student',
                'student.child',
            ],
            [
                'super_user',
                'other_school',
                new Adult(['user_id' => 'other_principal']),
                'principal',
                'principal.adult',
            ],
            [
                'principal',
                'math',
                new Adult(['user_id' => 'english_teacher']),
                'teacher',
                'teacher.adult',
            ],
            [
                'principal',
                'math',
                new Child(['user_id' => 'english_student']),
                'student',
                'student.child',
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidPostDataProvider()
    {
        return [
            [
                'principal',
                '/group/other_math/user/english_teacher',
                'teacher',
                'english_teacher',
                403
            ],
            [
                'principal',
                '/group/other_school/user/english_student',
                'student',
                'english_student',
                403
            ],
            [
                'principal',
                '/group/foo/user/english_student',
                'student',
                'english_student',
                404
            ],
            [
                'principal',
                '/group/math/user/foo',
                'student',
                'foo',
                404
            ],
            [
                'english_teacher',
                '/group/english/user/math_student',
                'student',
                'math_student',
                403
            ],
            [
                'english_teacher',
                '/group/math/user/english_student',
                'student',
                'english_student',
                403
            ],
            [
                'english_student',
                '/group/english/user/math_student',
                'student',
                'math_student',
                403
            ],
            [
                'english_student',
                '/group/math/user/english_student',
                'student',
                'english_student',
                403
            ],
            [
                'principal',
                '/group/math/user/english_teacher',
                'foo_role',
                'english_teacher',
                422
            ],
            [
                'principal',
                '/group/school/user/english_student',
                'teacher',
                'english_student',
                422
            ],
        ];
    }
}
