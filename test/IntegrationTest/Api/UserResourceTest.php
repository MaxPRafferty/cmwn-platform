<?php

namespace IntegrationTest\Api;

use IntegrationTest\AbstractApigilityTestCase as TestCase;

/**
 * Test UserResourceTest
 *
 * @group Integration
 * @group UserIntegration
 * @group API
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserResourceTest extends TestCase
{
    /**
     * @after
     */
    public function logOutUser()
    {
        $this->getAuthService()->clearIdentity();
    }
    
    /**
     * @test
     */
    public function testItShould401WhenTryingTooAccessEnglishStudentWhenNotLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->assertFalse($this->getAuthService()->hasIdentity());
        $this->dispatch('/user/english_student');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @param $access
     * @param $login
     * @test
     * @dataProvider getAccessProvider
     * @codingStandardsIgnoreStart
     */
    public function testItShouldReturnCorrectCodeWhenTryingTooAccessUser($login, $access, $code)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/user/' . $access);
        
        $this->assertResponseStatusCode($code);
        $this->assertMatchedRouteName('api.rest.user');
        $this->assertControllerName('api\v1\rest\user\controller');
        $this->assertNotRedirect();
    }

    /**
     * @return string[]
     */
    public function getAccessProvider()
    {
        return include __DIR__ . '/_providers/GET.access.provider.php';
    }
}
