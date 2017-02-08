<?php

namespace IntegrationTest\Sa\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use Zend\Json\Json;

/**
 * Class SuperAdminSettingsResourceTest
 * @package IntegrationTest\Sa\Rest
 */
class SuperAdminResourceTest extends AbstractApigilityTestCase
{
    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/sa.dataset.php');
    }

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
        $this->assertControllerName('sa\v1\rest\superadminsettings\controller');

        $body = $this->getResponse()->getContent();

        $body = Json::decode($body, Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_links', $body);

        $actual = [];
        $expected = ['self', 'user', 'games', 'game-data', 'group', 'org'];
        foreach ($body['_links'] as $key => $link) {
            $actual[] = $key;
        }

        $this->assertArrayHasKey('roles', $body);
        $this->assertEquals(
            $body['roles'],
            ['group' => ['admin', 'asst_principal', 'principal', 'student', 'teacher'],]
        );

        $this->assertEquals($expected, $actual);
    }
}
