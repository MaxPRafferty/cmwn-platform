<?php

namespace SecurityTest\Listeners;

use Application\Utils\NoopLoggerAwareTrait;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Listeners\ExpireAuthSessionListener;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Test ExpireAuthSessionListenerTest
 *
 * @group Security
 * @group Session
 * @group Authentication
 */
class ExpireAuthSessionListenerTest extends TestCase
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ExpireAuthSessionListener
     */
    protected $listener;

    /**
     * @var \Mockery\MockInterface|\Security\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @var \Mockery\MockInterface|\Zend\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @before
     */
    public function setUpContainer()
    {
        Container::setDefaultManager(null);
        $config = new StandardConfig([
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
        ]);

        $manager = new SessionManager($config);
        $this->container = new Container('Default', $manager);

    }

    /**
     * @before
     */
    public function setUpListener()
    {
        $this->authService = \Mockery::mock('\Security\Authentication\AuthenticationService');
        $this->listener = new ExpireAuthSessionListener($this->container);
        $this->listener->setAuthenticationService($this->authService);
        $this->logger = \Mockery::mock('\Zend\Log\LoggerInterface');
        $this->listener = new ExpireAuthSessionListener($this->container);
        $this->listener->setAuthenticationService($this->authService);
        $this->listener->setLogger($this->logger);
    }

    /**
     * @test
     */
    public function testItShouldExpireSession()
    {
        $ls = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->container->offsetSet('last_seen', $ls->getTimestamp() - ExpireAuthSessionListener::AUTH_TIMEOUT-1);
        $this->authService
            ->shouldReceive('hasIdentity')
            ->andReturn(true);
        $this->logger
            ->shouldReceive('debug')
            ->once();
        $this->authService
            ->shouldReceive('clearIdentity')
            ->once();
        $this->logger
            ->shouldReceive('info')
            ->once();
        $this->assertEquals($this->listener->onRoute(), new ApiProblemResponse(new ApiProblem(401, 'Expired')));
        $this->assertEquals($this->container->offsetExists(), false);
    }

    /**
     * @test
     */
    public function testItShouldReturnNullIfNoIdentity()
    {
        $this->authService
            ->shouldReceive('hasIdentity')
            ->andReturnNull();
        $this->assertEquals($this->listener->onRoute(), null);
        $this->assertEquals($this->container->offsetExists('last_seen'), false);
    }

    /**
     * @test
     */
    public function testItShouldNotExpireSessionIfNotTimeOut()
    {
        $this->authService
            ->shouldReceive('hasIdentity')
            ->andReturn(true);
        $this->logger
            ->shouldReceive('debug')
            ->once();
        $this->assertEquals($this->listener->onRoute(), null);
    }
}
