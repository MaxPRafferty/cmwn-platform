<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;

/**
 * Test GroupResourceTest
 *
 * @group Group
 * @group API
 * @group GroupService
 * @group IntegrationTest
 * @group Db
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupResourceTest extends TestCase
{

    /**
     * @test
     * @ticket CORE-993
     */
    public function testItShouldAllowPrincipalToAccessGroup()
    {
        $this->logInUser('principal');
        $this->injectValidCsrfToken();
        $this->dispatch('/group/english');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
    }
}
