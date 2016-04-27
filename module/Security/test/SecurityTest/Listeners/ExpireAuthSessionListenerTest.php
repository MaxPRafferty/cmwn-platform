<?php

namespace SecurityTest\Listeners;

use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Session\Container;
use Zend\Session\SessionManager;

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
     * @before
     */
    public function setUpContainer()
    {
        $this->markTestIncomplete('Not Set up');
        $manager         = new SessionManager();
        $this->container = new Container('expire_test', $manager);
    }

    /**
     * @test
     */
    public function testItShouldExpireSession()
    {
        $this->markTestIncomplete('Not Implementated');
    }
}
