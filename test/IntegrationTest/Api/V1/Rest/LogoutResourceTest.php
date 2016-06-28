<?php
/**
 * Created by PhpStorm.
 * User: chaitu
 * Date: 6/28/16
 * Time: 2:27 PM
 */

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * Test LogoutResourceTest
 * @group Logout
 */

class LogoutResourceTest extends TestCase
{
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
        $this->dispatch('/user/english_student');
        $this->assertResponseStatusCode(401);
    }
}
