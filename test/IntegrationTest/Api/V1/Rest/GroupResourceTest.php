<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * Test GroupResourceTest
 * @group DB
 */

class GroupResourceTest extends TestCase
{
    /**
     * @test
     * @ticket core-864
     */
    public function testItShouldReturnValidGroups()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/group');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('group', $body['_embedded']);
        $groups = $body['_embedded']['group'];
        $expectedIds = ['english','school'];
        $actualIds = [];
        foreach ($groups as $group) {
            $this->assertArrayHasKey('group_id', $group);
            $actualIds[] = $group['group_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }
    
    /**
     * @test
     * @ticket core-864
     */
    public function testItShouldReturnSchoolForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/group?type=school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('group', $body['_embedded']);
        $groups = $body['_embedded']['group'];
        $expectedIds = ['school'];
        $actualIds = [];
        foreach ($groups as $group) {
            $this->assertArrayHasKey('group_id', $group);
            $actualIds[] = $group['group_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }
}
