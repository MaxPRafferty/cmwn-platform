<?php

namespace SecurityTest\Guard;

use IntegrationTest\SessionManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\Guard\XsrfGuard;
use Zend\Http\Header\Cookie;
use \Zend\Router\Http\RouteMatch;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Test XsrfGuardTest
 *
 * @group Security
 * @group Authentication
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class XsrfGuardTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SessionManager
     */
    protected $manager;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Mockery\MockInterface|\Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * @var \Security\Guard\XsrfGuard
     */
    protected $xsrf;

    /**
     * @before
     */
    public function setUpManager()
    {
        Container::setDefaultManager(null);
        $config = new StandardConfig([
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
        ]);

        $this->manager   = $manager = new SessionManager($config);
        $this->container = new Container('Default', $manager);
    }

    /**
     * @before
     */
    public function setUpEvent()
    {
        $this->event = \Mockery::mock('\Zend\Mvc\MvcEvent');
    }

    /**
     * @before
     */
    public function setUpXsrf()
    {
        $this->xsrf = new XsrfGuard([]);
    }

    /**
     * @test
     */
    public function testItShouldNotFail()
    {
        $response   = new HttpResponse();
        $request    = \Mockery::mock('Zend\Http\PhpEnvironment\Request');
        $routeMatch = new RouteMatch([]);

        $this->event
            ->shouldReceive('getResponse')
            ->andReturn($response);
        $this->assertNull($this->xsrf->onFinish($this->event));

        $cookie = $response->getCookie();

        $this->event
            ->shouldReceive('getRequest')
            ->andReturn($request);
        $this->event
            ->shouldReceive('getRouteMatch')
            ->andReturn($routeMatch);
        $request
            ->shouldReceive('getCookie')
            ->andReturn($cookie);
        $this->assertNull($this->xsrf->onDispatch($this->event));
    }

    /**
     * @test
     */
    public function testItShouldReturnApiProblemIfNoToken()
    {
        $request    = \Mockery::mock('Zend\Http\PhpEnvironment\Request');
        $routeMatch = new RouteMatch([]);
        $cookie     = new Cookie();
        $cookie->offsetSet('XSRF-TOKEN', null);

        $this->event
            ->shouldReceive('getRequest')
            ->andReturn($request);
        $this->event
            ->shouldReceive('getRouteMatch')
            ->andReturn($routeMatch);
        $request
            ->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->assertEquals(
            $this->xsrf->onDispatch($this->event),
            new ApiProblemResponse(new ApiProblem(500, 'Invalid Token'))
        );
    }
}
