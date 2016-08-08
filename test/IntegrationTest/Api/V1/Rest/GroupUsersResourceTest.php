<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use Security\Exception\ChangePasswordException;
use Zend\Json\Json;

/**
 * Test GroupUsersResourceTest
 *
 * @group DB
 * @group UserGroup
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
        $this->assertResponseStatusCode(421);
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
    public function testItShouldReturnUsersOfHisGroup($user, $group, $expectedIds)
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
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/group/school/users',
            ],
        ];
    }

    public function userDataProvider()
    {
        return [
            'math student' => [
                'math_student',
                'math',
                ['math_teacher'],
            ],
            'principal'    => [
                'principal',
                'school',
                ['english_teacher', 'english_student', 'math_teacher', 'math_student'],
            ],
        ];
    }
}
