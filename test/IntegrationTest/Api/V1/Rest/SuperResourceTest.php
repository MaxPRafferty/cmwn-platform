<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Security\Service\SecurityService;
use Zend\Json\Json;

/**
 * Class SuperResourceTest
 * @package IntegrationTest\Api\V1\Rest
 * @SuppressWarnings(PHPMD)
 */
class SuperResourceTest extends AbstractApigilityTestCase
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
     * @inheritdoc
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/default.dataset.php');
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('super_user');
        $this->dispatch('/super/english_student');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/super/english_student');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('super_user');
        $this->dispatch('/super/english_student');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSuperUsers()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->securityService->setSuper('principal');
        $this->dispatch('/super');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('super', $body['_embedded']);

        $superUsers = $body['_embedded']['super'];
        $actual = ['principal'];
        foreach ($superUsers as $super) {
            $expected[] = $super['user_id'];
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShouldFetchSuperUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/super/super_user');
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('user_id', $body);
        $this->assertEquals('super_user', $body['user_id']);
    }

    /**
     * @test
     */
    public function testItShould404IfUserIsNotASuper()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/super/english_student');
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     */
    public function testItShould404IfUserNotFound()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/super/foo');
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     */
    public function testItShouldNotLetNonSupersToFetchSuperUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/super/super_user');
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');
        $this->assertResponseStatusCode(403);
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
        $this->dispatch('/super/' . $login, 'POST');
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');

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
        $this->dispatch('/super/super_user', 'DELETE');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');

        $userAfter = $this->securityService->fetchUserByUserName('super_user');
        $this->assertFalse($userAfter->isSuper());
    }

    /**
     * @test
     * @param $login
     * @dataProvider nonSuperChildDataProvider
     */
    public function testItShould403ForFetchOnChildUsers($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/super/' . $login, 'POST');
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.super');
        $this->assertControllerName('api\v1\rest\super\controller');
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
            $this->dispatch('/super/super_user', 'POST');
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
