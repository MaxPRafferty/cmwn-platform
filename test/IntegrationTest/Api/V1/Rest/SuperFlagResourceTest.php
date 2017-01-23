<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Security\Service\SecurityService;

/**
 * Class SuperFlagResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class SuperFlagResourceTest extends TestCase
{
    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->securityService = TestHelper::getDbServiceManager()->get(SecurityService::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/users.dataset.php');
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('super_user');
        $this->dispatch('/user/english_student/super', 'POST', ['super' => true]);
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/super', 'POST', ['super' => true]);
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('super_user');
        $this->dispatch('/user/english_student/super', 'POST', ['super' => true]);
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     * @param $login
     * @dataProvider nonSuperAdultDataProvider
     */
    public function testItShouldSetSuperFlagForUser($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $userBefore = $this->securityService->fetchUserByUserName($login);
        $this->assertFalse($userBefore->isSuper());
        $this->dispatch('/user/' . $login . '/super', 'POST', ['super' => true]);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.super-flag');
        $this->assertControllerName('api\v1\rest\superflag\controller');

        $userAfter = $this->securityService->fetchUserByUserName($login);
        $this->assertTrue($userAfter->isSuper());
    }

    /**
     * @test
     */
    public function testItShouldUnsetSuperFlagForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $userBefore = $this->securityService->fetchUserByUserName('super_user');
        $this->assertTrue($userBefore->isSuper());
        $this->dispatch('/user/super_user/super', 'POST', ['super' => false]);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.super-flag');
        $this->assertControllerName('api\v1\rest\superflag\controller');

        $userAfter = $this->securityService->fetchUserByUserName('english_student');
        $this->assertFalse($userAfter->isSuper());
    }

    /**
     * @test
     * @param $login
     * @dataProvider nonSuperAdultDataProvider
     */
    public function testItShouldNotChangeTheStatusIfTheFlagMatchesCurrentSuperBit($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $userBefore = $this->securityService->fetchUserByUserName('english_student');
        $this->assertFalse($userBefore->isSuper());
        $this->dispatch('/user/' . $login . '/super', 'POST', ['super' => false]);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.super-flag');
        $this->assertControllerName('api\v1\rest\superflag\controller');

        $userAfter = $this->securityService->fetchUserByUserName($login);
        $this->assertFalse($userAfter->isSuper());
    }

    /**
     * @test
     * @param $login
     * @dataProvider nonSuperChildDataProvider
     */
    public function testItShould403ForChildUsers($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user/' . $login . '/super', 'POST', ['super' => false]);
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.super-flag');
        $this->assertControllerName('api\v1\rest\superflag\controller');
    }

    /**
     * @test
     */
    public function testItShouldNotLetOthersToSetSuperFlag()
    {
        $users = array_merge($this->nonSuperAdultDataProvider(), $this->nonSuperChildDataProvider());

        foreach ($users as $user) {
            $this->injectValidCsrfToken();
            $this->logInUser($user[0]);
            $this->dispatch('/user/english_student/super', 'POST', ['super' => false]);
            $this->assertResponseStatusCode(403);
        }
    }

    /**
     * @return array
     */
    public function nonSuperAdultDataProvider()
    {
        return [
            'Principal'       => [
                'principal',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
            'Other Principal' => [
                'other_principal',
            ],
            'Other Teacher' => [
                'other_teacher',
            ],
        ];
    }

    /**
     * @return array
     */
    public function nonSuperChildDataProvider()
    {
        return [
            'English Student'       => [
                'english_student',
            ],
            'Other Student'         => [
                'other_student',
            ],
        ];
    }
}
