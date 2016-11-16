<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Security\SecurityUser;
use Security\Service\SecurityService;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Test PasswordResourceTest
 *
 * @group Security
 * @group Api
 * @group PasswordResource
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PasswordResourceTest extends TestCase
{
    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/password.dataset.php');
    }

    /**
     * @before
     */
    public function setUpSecurityService()
    {
        $this->securityService = TestHelper::getServiceManager()->get(SecurityService::class);
    }

    /**
     * @test
     * @ticket CORE-1024
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch(
            '/user/english_student/password',
            'POST',
            ['password' => 'apple0007', 'password_confirmation' => 'apple0007']
        );
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     * @ticket CORE-707
     */
    public function testItShouldResetPasswordForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch(
            '/user/english_student/password',
            'POST',
            ['password' => 'apple0007', 'password_confirmation' => 'apple0007']
        );

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.password');
        $this->assertControllerName('api\v1\rest\password\controller');

        /** @var SecurityUser $user */
        $user = $this->securityService->fetchUserByUserName('english_student');

        $this->assertEquals('english_student', $user->getUserId());
        $this->assertTrue($user->comparePassword('apple0007'));
    }

    /**
     * @test
     */
    public function testItShouldNotAllowToResetPasswordIfNotLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch(
            '/user/english_student/password',
            'POST',
            ['password' => 'apple0007', 'password_confirmation' => 'apple0007']
        );
        $this->assertResponseStatusCode(401);
    }
}
