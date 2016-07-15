<?php

namespace IntegrationTest\Api\V1\Rest;

use Forgot\Service\ForgotServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Security\Service\SecurityService;

/**
 * Test ResetResourceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ResetResourceTest extends TestCase
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
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->markTestIncomplete("not checked");
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_teacher');
        $this->dispatch('/user/english_student/reset', 'POST', ['code' => 'apple0007']);
        $this->assertResponseStatusCode(201);
    }

    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldAllowTeacherToResetChild()
    {
        $user = $this->securityService->fetchUserByUserName('english_student');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_student" must not have a login code in order to run this test'
        );

        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');
        $this->dispatch('/user/english_student/reset', 'POST', ['code' => 'apple0007']);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.reset');
        $this->assertControllerName('api\v1\rest\reset\controller');

        $user = $this->securityService->fetchUserByUserName('english_student');
        $this->assertEquals(
            'apple0007',
            $user->getCode(),
            'User "english_student" did not get the correct code set'
        );
    }

    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldAllowAdminsToResetCode()
    {
        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_teacher" must not have a login code in order to run this test'
        );

        $this->injectValidCsrfToken();
        $this->logInUser('principal');
        $this->dispatch('/user/english_teacher/reset', 'POST', ['code' => 'apple0007']);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.reset');
        $this->assertControllerName('api\v1\rest\reset\controller');

        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertEquals(
            'apple0007',
            $user->getCode(),
            'User "english_teacher" did not get the correct code set'
        );
    }

    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldDenyNonNeighborsFromSendingCode()
    {
        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_teacher" must not have a login code in order to run this test'
        );

        $this->injectValidCsrfToken();
        $this->logInUser('other_teacher');
        $this->dispatch('/user/english_teacher/reset', 'POST', ['code' => 'apple0007']);
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.reset');
        $this->assertControllerName('api\v1\rest\reset\controller');

        $user = $this->securityService->fetchUserByUserName('english_teacher');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_teacher" Had their code reset from a forigen user'
        );
    }

    /**
     * @test
     * @ticket CORE-672
     */
    public function testItShouldDenyOtherTeachersFromResetingOtherStudents()
    {
        $user = $this->securityService->fetchUserByUserName('english_student');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_student" must not have a login code in order to run this test'
        );

        $this->injectValidCsrfToken();
        $this->logInUser('other_teacher');
        $this->dispatch('/user/english_student/reset', 'POST', ['code' => 'apple0007']);
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.reset');
        $this->assertControllerName('api\v1\rest\reset\controller');

        $user = $this->securityService->fetchUserByUserName('english_student');
        $this->assertEmpty(
            $user->getCode(),
            'User "english_student" Had their code reset from a forigen user'
        );
    }
}
