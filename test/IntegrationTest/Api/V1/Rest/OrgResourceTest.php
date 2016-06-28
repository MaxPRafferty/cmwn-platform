<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * Test OrgResourceTest
 * @group Org
 * @incompleteTest
 */

class OrgResourceTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCheckIfUserLoggedIn()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/org/district');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('english_student');

        $this->dispatch('/org/district');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldFetchOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/org/district');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('org_id', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('type', $body);
        $this->assertEquals('district', $body['org_id']);
        $this->assertEquals('Gina\'s District', $body['title']);
        $this->assertEquals('district', $body['type']);
        $this->assertEquals(null, $body['description']);
    }

    /**
     * @test
     */
    public function testItShould404FetchOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/org/foo');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     * @ticket CORE-884
     * @incompleteTest
     */
    public function testItShould403WhenUserFetchOtherOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/org/manchuck');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);
    }
    /**
     * @test
     */
    public function testItShouldFetchAllOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch('/org');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('org', $body['_embedded']);
        $expectedIds = ['district', 'manchuck'];
        foreach ($body['_embedded']['org'] as $org) {
            $this->assertArrayHasKey('org_id', $org);
            $actualIds[] = $org['org_id'];
        }
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * @test
     */
    public function testItShouldFetchTheirOrgForOthers()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/org');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('org', $body['_embedded']);
        $expectedIds = ['district'];
        foreach ($body['_embedded']['org'] as $org) {
            $this->assertArrayHasKey('org_id', $org);
            $actualIds[] = $org['org_id'];
        }
        $this->assertEquals($expectedIds, $actualIds);
    }
}
