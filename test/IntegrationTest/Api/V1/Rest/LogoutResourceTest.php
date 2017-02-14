<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Test LogoutResourceTest
 * @group Logout
 */

class LogoutResourceTest extends TestCase
{

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/login.dataset.php');
    }

    /**
     * @test
     */
    public function testItShouldLogout()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/logout');
        $this->assertMatchedRouteName('api.rest.logout');
        $this->assertControllerName('api\v1\rest\logout\controller');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordExceptionGroupId()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/logout');
        $this->assertResponseStatusCode(200);
    }
}
