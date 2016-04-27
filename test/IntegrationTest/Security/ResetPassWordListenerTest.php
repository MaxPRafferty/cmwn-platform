<?php

namespace IntegrationTest\Security;

use IntegrationTest\TestHelper;
use Security\ChangePasswordUser;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Http\Headers;
use Zend\Json\Json;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Exception ResetPassWordListenerTest
 *
 * @group IntegrationTest
 * @group NotDefault
 */
class ResetPassWordListenerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;

    protected $routes = [];

    /**
     * @var \Mockery\MockInterface|\Zend\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @before
     */
    public function setUpApplication()
    {
        $this->markTestIncomplete('Add Valid Token crap');
        $this->setApplicationConfig(TestHelper::getApplicationConfig());
    }

    /**
     * Make sure that we have all routes that match up.  If we
     * @before
     */
    public function setUpRoutes()
    {
        $routes      = TestHelper::getRoutes();
        $checkRoutes = [];
        array_walk($routes, function ($routeConfig) use (&$checkRoutes) {
            $options        = isset($routeConfig['options']) ? $routeConfig['options'] : [];
            $defaults       = isset($options['defaults']) ? $options['defaults'] : [];
            $controllerName = isset($defaults['controller']) ? $defaults['controller'] : false;

            if ($controllerName === false) {
                return;
            }

            $checkRoutes[] = $controllerName;
        });

        $checkRoutes = array_unique($checkRoutes);
//        $this->assertEquals(
//            $checkRoutes,
//            array_keys($this->routesListDataProvider()),
//            'Seems that a new route was added, update this data provider to include the expectation for that route'
//        );
    }

    /**
     * @before
     */
    public function setUpResetUser()
    {
        $user = new ChangePasswordUser();

        /** @var AuthenticationService $authService */
        $authService = static::getApplicationServiceLocator()->get('Security\Authentication\AuthenticationService');
        $authService->setStorage(new NonPersistent());
        $authService->getStorage()->write($user);
    }

    public function tearDown()
    {
        /** @var AuthenticationService $authService */
        $authService = static::getApplicationServiceLocator()->get('Security\Authentication\AuthenticationService');
        $authService->clearIdentity();
    }

    /**
     * @dataProvider routesListDataProvider
     */
    public function testItShouldAlwaysReturn401WhenLoggedInUserNeedsToChangePassword($url, $code, $detail)
    {
        /** @var Headers $headers */
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept: application/json');
        $this->dispatch($url);
        $content = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertResponseStatusCode($code);
        if ($detail !== false) {
            $this->assertArrayHasKey('detail', $content, 'Missing detail in response');
            $this->assertEquals($detail, $content['detail'], 'Detail is invalid');
        }
    }

    public function routesListDataProvider()
    {
        return [
            'Api\V1\Rest\User\Controller'       => ['url' => '/user', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Application\Controller\Index'      => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Org\Controller'        => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Game\Controller'       => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Image\Controller'      => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Group\Controller'      => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Token\Controller'      => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Login\Controller'      => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Logout\Controller'     => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Forgot\Controller'     => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Password\Controller'   => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\GroupUsers\Controller' => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\OrgUsers\Controller'   => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\UserImage\Controller'  => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\Import\Controller'     => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
//            'Api\V1\Rest\UserName\Controller'   => ['url' => '/', 'code' => 401, 'detail' => 'RESET_PASSWORD'],
        ];
    }
}
