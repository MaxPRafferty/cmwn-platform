<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use Zend\Json\Json;

/**
 * Test GroupUsersResourceTest
 *
 * @group DB
 * @group UserGroup
 * @group IntegrationTest
 * @group Api
 * @group Group
 * @group User
 */
class GroupUsersResourceTest extends AbstractApigilityTestCase
{
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

        $this->dispatch('/group/math/users');
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

        $this->dispatch('/group/foo/users');
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

        $this->dispatch('/group/' . $group . '/users');
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
     * @dataProvider childDataProvider
     */
    public function testItShouldDenyReturningUsersAGroupForAdults($user, $group)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);

        $this->dispatch('/group/' . $group . '/users');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldNotReturnUsersOfOtherGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/group/english/users');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-2331
     * @group MissingApiRoute
     */
    public function testItShouldAttachUserToGroup()
    {
        $this->markTestIncomplete("Add an api route to post to this endpoint with (user_id||user)&&role passed in");
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'user' => 'english_student',
            'role' => 'student'
        ];
        $this->dispatch('/group/school/users', 'POST', $postData);
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            [
                'english_student',
                '/group/school/users',
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
}
