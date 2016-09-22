<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper as TestHelper;
use Security\Service\SecurityService;
use IntegrationTest\DataSets\ArrayDataSet;
use Zend\Json\Json;

/**
 * Test LoginResourceTest
 *
 * @group DB
 * @group IntegrationTest
 * @group API
 * @group User
 * @group Login
 */
class LoginResourceTest extends TestCase
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
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../../../DataSets/login.dataset.php';

        return new ArrayDataSet($data);
    }

    /**
     * @test
     * @ticker CORE-1165
     * @dataProvider loginDataProvider
     */
    public function testItShouldLoginUser($login)
    {
        $this->dispatch(
            '/login',
            'POST',
            ['username' => $login, 'password' => 'business']
        );
        $this->assertMatchedRouteName('api.rest.login');
        $this->assertControllerName('api\v1\rest\login\controller');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @test
     */
    public function testItShould401InvalidPassword()
    {
        $this->dispatch(
            '/login',
            'POST',
            ['username' => 'english_student', 'password' => 'foo']
        );
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShould401InvalidLogin()
    {
        $this->dispatch(
            '/login',
            'POST',
            ['username' => 'foo', 'password' => 'foo']
        );
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldGoToChangePasswordUponCode()
    {
        $this->securityService->saveCodeToUser('xyz', 'english_teacher');
        $this->dispatch(
            '/login',
            'POST',
            ['username' => 'english_teacher', 'password' => 'xyz']
        );
        $this->assertResponseStatusCode(401);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     */
    public function testItShouldLoginWithCorrectPasswordUponCode()
    {
        $this->securityService->saveCodeToUser('xyz', 'english_teacher');
        $this->dispatch(
            '/login',
            'POST',
            ['username' => 'english_teacher', 'password' => 'business']
        );
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.login');
        $this->assertControllerName('api\v1\rest\login\controller');
    }

    /**
     * @test
     */
    public function testItShouldNotGoToChangePasswordUponWrongCode()
    {
        $this->securityService->saveCodeToUser('xyz', 'english_teacher');
        $this->dispatch(
            '/login',
            'POST',
            ['username' => 'english_teacher', 'password' => 'foo']
        );
        $this->assertResponseStatusCode(401);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('Invalid Login', $body['detail']);
    }

    /**
     * @test
     */
    public function testItShouldNotGoToChangePasswordUponCodeExpiry()
    {
        $this->dispatch(
            '/login',
            'POST',
            ['username' => 'math_student', 'password' => 'pqr']
        );
        $this->assertResponseStatusCode(401);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('Invalid Login', $body['detail']);
    }

    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            0 => [
                'user' => 'fancy-lion002',
            ],
            1 => [
                'user' => 'fancy-lion+002',
            ],
            2 => [
                'user' => 'english _ student',
            ],
            3 => [
                'user' => 'English - Student',
            ]
        ];
    }
}
