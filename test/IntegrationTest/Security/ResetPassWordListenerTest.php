<?php

namespace IntegrationTest\Security;

use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\TestHelper;
use Zend\Json\Json;

/**
 * Exception ResetPassWordListenerTest
 *
 * @group IntegrationTest
 * @group Security
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ResetPassWordListenerTest extends AbstractApigilityTestCase
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * Make sure that we have all routes that match up.  If we
     * @before
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setUpRoutes()
    {
        $openRoutes = [
            'Api\V1\Rest\Image\Controller',
            'Api\V1\Rest\Login\Controller',
            'Api\V1\Rest\Logout\Controller',
            'Api\V1\Rest\Forgot\Controller',
            'Api\V1\Rest\Import\Controller',
            'Api\V1\Rest\UpdatePassword\Controller',
            'Api\V1\Rest\Media\Controller',
        ];

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
        $allRoutes = array_merge($openRoutes, array_keys($this->routesListDataProvider()));
        $this->assertEquals(
            sort($checkRoutes),
            sort($allRoutes),
            'Seems that a new route was added, update this data provider to include the expectation for that route'
        );
    }

    /**
     * @dataProvider routesListDataProvider
     */
    public function testItShouldAlwaysReturn401WhenLoggedInUserNeedsToChangePassword($url, $code, $detail, $controller)
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('super_user');
        if (!is_array($url)) {
            $this->dispatch($url);
        } else {
            $this->dispatch($url[0], $url[1], $url[2]);
        }
        $content = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertResponseStatusCode($code);
        $this->assertControllerName($controller);
        if ($detail !== false) {
            $this->assertArrayHasKey('detail', $content, 'Missing detail in response');
            $this->assertEquals($detail, $content['detail'], 'Detail is invalid');
        }
    }

    /**
     * @return array
     */
    public function routesListDataProvider()
    {
        return [
            'Application\Controller\Index' => [
                'url' => '/',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Token\Controller'
            ],
            'Api\V1\Rest\User\Controller'       => [
                'url' => '/user',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\User\Controller'
            ],
            'Api\V1\Rest\Org\Controller'        => [
                'url' => '/org',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Org\Controller'
            ],
            'Api\V1\Rest\Game\Controller'       => [
                'url' => '/game',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Game\Controller'
            ],
            'Api\V1\Rest\Group\Controller'      => [
                'url' => '/group',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Group\Controller'
            ],
            'Api\V1\Rest\Token\Controller'      => [
                'url' => '/',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Token\Controller'
            ],
            'Api\V1\Rest\Password\Controller'   => [
                'url' => [
                    '/user/english_student/password',
                    'POST',
                    ['password' => 'apple0007', 'password_confirmation' => 'apple0007']],
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Password\Controller'
            ],
            'Api\V1\Rest\GroupUsers\Controller' => [
                'url' => '/group/school/users',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\GroupUsers\Controller'
            ],
            'Api\V1\Rest\OrgUsers\Controller'   => [
                'url' => '/org/district/users',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\OrgUsers\Controller'
            ],
            'Api\V1\Rest\UserImage\Controller'  => [
                'url' => ['/user/super_user/image', 'POST', ['image_id' => 'foobar', 'url' => 'www.example.com']],
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\UserImage\Controller'
            ],
            'Api\V1\Rest\UserName\Controller'   => [
                'url' => '/user-name',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\UserName\Controller'
            ],
            'Api\V1\Rest\Flip\Controller'       => [
                'url' => '/flip',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Flip\Controller'
            ],
            'Api\V1\Rest\FlipUser\Controller'   => [
                'url' => '/user/english_student/flip',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\FlipUser\Controller'
            ],
            'Api\V1\Rest\Friend\Controller'     => [
                'url' => '/user/english_student/friend',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Friend\Controller'
            ],
            'Api\V1\Rest\Suggest\Controller'    => [
                'url' => '/user/english_student/suggest',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Suggest\Controller'
            ],
            'Api\V1\Rest\Reset\Controller'      => [
                'url' => '/user/super_user/reset',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Reset\Controller'
            ],
            'Api\V1\Rest\SaveGame\Controller'   => [
                'url' => '/user/english_student/game/monarch',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\SaveGame\Controller'
            ],
            'Api\V1\Rest\GameData\Controller'   => [
                'url' => '/game-data/animal-id',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\GameData\Controller'
            ],
            'Api\V1\Rest\Skribble\Controller'   => [
                'url' => '/user/english_student/skribble',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Skribble\Controller'
            ],
            'Api\V1\Rest\SkribbleNotify\Controller' => [
                'url' => ['/user/english_student/skribble/foo-bar/notice', 'POST', ['status' => 'success']],
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\SkribbleNotify\Controller'
            ],
            'Api\V1\Rest\Feed\Controller'       => [
                'url' => '/user/super_user/feed',
                'code' => 401,
                'detail' => 'RESET_PASSWORD',
                'controller' => 'Api\V1\Rest\Feed\Controller'
            ],
        ];
    }
}
