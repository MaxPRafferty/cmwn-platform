<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use Zend\Json\Json;

/**
 * Class SuperResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class SuperResourceTest extends AbstractApigilityTestCase
{
    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/default.dataset.php');
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
}
