<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use Zend\Json\Json;

/**
 * Test GroupUsersResourceTest
 * @group DB
 * @group UserGroup
 */

class GroupUsersResourceTest extends AbstractApigilityTestCase
{
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
     */
    public function testItShouldReturnUsersOfHisGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/group/math/users');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('items', $body['_embedded']);
        $groupUsers = $body['_embedded']['items'];
        $expectedIds = ['math_teacher'];
        $actualIds = [];
        foreach ($groupUsers as $user) {
            $this->assertArrayHasKey('user_id', $user);
            $actualIds[] = $user['user_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }

    /**
     * @test
     */
    public function testItShouldReturnUsersOfOtherGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/group/english/users');
        $this->assertMatchedRouteName('api.rest.group-users');
        $this->assertControllerName('api\v1\rest\groupusers\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('items', $body['_embedded']);
        $groupUsers = $body['_embedded']['items'];
        $expectedIds = ['english_teacher','english_student'];
        $actualIds = [];
        foreach ($groupUsers as $user) {
            $this->assertArrayHasKey('user_id', $user);
            $actualIds[] = $user['user_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }
}
