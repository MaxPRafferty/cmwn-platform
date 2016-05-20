<?php

namespace IntegrationTest\Api;

use Application\Exception\NotFoundException;
use Forgot\Service\ForgotServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Security\Service\SecurityService;

/**
 * Test ForgotResourceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ForgotResourceTest extends TestCase
{
    /**
     * @var ForgotServiceInterface
     */
    protected $forgotService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @before
     */
    public function setUpForgotService()
    {
        $this->forgotService = TestHelper::getServiceManager()->get(ForgotServiceInterface::class);
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
     * @ticket CORE-672
     */
    public function testItShouldCreatedRandomCodeWhenUserRequestsEmail()
    {
        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_teacher" must not have a login code in order to run this test'
        );

        $this->dispatch('/forgot', 'POST', ['email' => $user->getEmail()]);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.forgot');
        $this->assertControllerName('api\v1\rest\forgot\controller');

        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertNotEmpty(
            $user->getCode(),
            'User "english_teacher" did not get a random code generated'
        );
    }
    
    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldCreateRandomCodeBasedOnUserName()
    {
        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_teacher" must not have a login code in order to run this test'
        );

        $this->dispatch('/forgot', 'POST', ['email' => 'english_teacher']);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.forgot');
        $this->assertControllerName('api\v1\rest\forgot\controller');

        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertNotEmpty(
            $user->getCode(),
            'User "english_teacher" did not get a random code generated'
        );
    }
    
    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldReturnSuccessWhenEmailAddressDoesNotExist()
    {
        try {
            $this->securityService->fetchUserByUserName('foo@example.com');
            $this->fail('There seems to be a user with the email foo@example.com');
        } catch (NotFoundException $notFound) {

        }

        $this->dispatch('/forgot', 'POST', ['email' => 'foo@example.com']);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.forgot');
        $this->assertControllerName('api\v1\rest\forgot\controller');
    }

    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldReturnSuccessWhenUserNameDoesNotExist()
    {
        try {
            $this->securityService->fetchUserByUserName('foo_bar');
            $this->fail('There seems to be a user with the username foo_bar');
        } catch (NotFoundException $notFound) {

        }

        $this->dispatch('/forgot', 'POST', ['email' => 'foo_bar']);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.forgot');
        $this->assertControllerName('api\v1\rest\forgot\controller');
    }
}
