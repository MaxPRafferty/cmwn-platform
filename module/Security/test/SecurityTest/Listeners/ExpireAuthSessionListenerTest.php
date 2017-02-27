<?php

namespace SecurityTest\Listeners;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\Authentication\AuthenticationService;
use Security\Listeners\ExpireAuthSessionListener;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;
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
    use MockeryPHPUnitIntegration;

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
     * @before
     */
    public function setUpListener()
    {
        $this->authService = \Mockery::mock(AuthenticationService::class);
        $this->listener    = new ExpireAuthSessionListener($this->container);
        $this->listener->setAuthenticationService($this->authService);
    }

    /**
     * @before
     */
    public function setUpContainer()
    {
        Container::setDefaultManager(null);
        $config = new StandardConfig([
            'storage' => ArrayStorage::class
        ]);

        $manager         = new SessionManager($config);
        $this->container = new Container('Default', $manager);
    }

    /**
     * @test
     */
    public function testItShouldExpireSession()
    {
        $currentTimestamp = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->container->offsetSet(
            'last_seen',
            $currentTimestamp->getTimestamp() - ExpireAuthSessionListener::AUTH_TIMEOUT - 1
        );

        $this->authService
            ->shouldReceive('hasIdentity')
            ->andReturn(true);

        $this->authService
            ->shouldReceive('clearIdentity')
            ->once();

        $this->assertEquals($this->listener->__invoke(), new ApiProblemResponse(new ApiProblem(401, 'Expired')));
        $this->assertEquals($this->container->offsetExists('last_seen'), false);
    }

    /**
     * @test
     */
    public function testItShouldReturnNullIfNoIdentity()
    {
        $this->authService
            ->shouldReceive('hasIdentity')
            ->andReturnNull();

        $this->assertEquals($this->listener->__invoke(), null);
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

        $this->assertEquals($this->listener->__invoke(), null);
    }
}
