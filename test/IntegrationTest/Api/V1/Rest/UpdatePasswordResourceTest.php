<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Security\ChangePasswordUser;
use Security\SecurityUser;
use Security\Service\SecurityService;

/**
 * Test UpdatePasswordResourceTest
 *
 * @group Security
 * @group Api
 * @group IntegrationTest
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UpdatePasswordResourceTest extends TestCase
{
    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @before
     */
    public function setUpSecurityService()
    {
        $this->securityService = TestHelper::getServiceManager()->get(SecurityService::class);
    }

    /**
     * @test
     * @ticket CORE-710
     */
    public function testItShouldChangePasswordForCodeUser()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');

        $this->dispatch(
            '/password',
            'POST',
            ['password' => 'pear0007', 'password_confirmation' => 'pear0007']
        );

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.update-password');
        $this->assertControllerName('api\v1\rest\updatepassword\controller');

        /** @var SecurityUser $user */
        $user = $this->securityService->fetchUserByUserName('english_student');

        $this->assertEquals('english_student', $user->getUserId());
        $this->assertTrue($user->comparePassword('pear0007'), 'The Password was not updated');
    }

    /**
     * @test
     * @ticket CORE-710
     */
    public function testItShouldErrorWhenConfirmationDoseNotMatchPassword()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');

        $this->dispatch(
            '/password',
            'POST',
            ['password' => 'pear0007', 'password_confirmation' => 'foobar']
        );

        $this->assertResponseStatusCode(422);
        $this->assertMatchedRouteName('api.rest.update-password');
        $this->assertControllerName('api\v1\rest\updatepassword\controller');

        /** @var SecurityUser $user */
        $user = $this->securityService->fetchUserByUserName('english_student');

        $this->assertEquals('english_student', $user->getUserId());
        $this->assertTrue($user->comparePassword('business'), 'The Password changed');
        $this->assertFalse($user->comparePassword('pear0007'), 'The Password changed');
    }

    /**
     * @test
     * @ticket CORE-710
     */
    public function testItShouldErrorWhenPasswordMatchesCode()
    {
        /** @var SecurityUser $user */
        $user = $this->securityService->fetchUserByUserName('english_student');
        $this->securityService->saveCodeToUser('Apple0007', $user);

        $user = new ChangePasswordUser(array_merge(['code' => 'Apple0007'], $user->getArrayCopy()));
        $this->getAuthService()->getStorage()->write($user);

        $this->injectValidCsrfToken();

        $this->dispatch(
            '/password',
            'POST',
            ['password' => 'Apple0007', 'password_confirmation' => 'Apple0007']
        );

        $this->assertResponseStatusCode(422);
        $this->assertMatchedRouteName('api.rest.update-password');
        $this->assertControllerName('api\v1\rest\updatepassword\controller');

        /** @var SecurityUser $user */
        $user = $this->securityService->fetchUserByUserName('english_student');

        $this->assertEquals('english_student', $user->getUserId());
        $this->assertTrue($user->comparePassword('business'), 'The Password changed');
        $this->assertFalse($user->comparePassword('Apple0007'), 'The Password changed');
    }
}
