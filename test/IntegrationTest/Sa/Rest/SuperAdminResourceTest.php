<?php

namespace IntegrationTest\Sa\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use Zend\Json\Json;

/**
 * Class SuperAdminSettingsResourceTest
 * @package IntegrationTest\Sa\Rest
 */
class SuperAdminSettingsResourceTest extends AbstractApigilityTestCase
{
    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->dispatch('/sa/settings');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfUserLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/sa/settings');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePass()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('super_user');
        $this->assertChangePasswordException('/sa/settings');
    }

    /**
     * @test
     */
    public function testItShouldGetSaSettingsLinks()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/sa/settings');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('sa.rest.settings');
        $this->assertControllerName('sa\rest\superadminsettings\controller');

        $body = $this->getResponse()->getContent();

        $body = Json::decode($body, Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_links', $body);

        $actual = [];
        $expected = ['Manage Users', 'Survey Results'];

        foreach ($body['_links'] as $link) {
            $actual[] = $link['label'];
        }

        $this->assertEquals($expected, $actual);
    }
}
