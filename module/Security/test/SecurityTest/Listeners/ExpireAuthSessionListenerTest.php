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
class ExpireAuthSessionListenerTest extends TestCase implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

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
        $this->listener->setLogger($this->getLogger());
    }

    /**
     * @test
     */
    public function testItShouldExpireSession()
    {
        $currentTimestamp = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->container->offsetSet(
            'last_seen',
            $currentTimestamp->getTimestamp() - ExpireAuthSessionListener::AUTH_TIMEOUT-1
        );
        $this->authService
            ->shouldReceive('hasIdentity')
            ->andReturn(true);
        $this->authService
            ->shouldReceive('clearIdentity')
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
        $this->assertEquals($this->listener->onRoute(), null);
    }
}
