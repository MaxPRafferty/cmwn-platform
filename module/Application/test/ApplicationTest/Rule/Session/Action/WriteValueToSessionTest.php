<?php

namespace ApplicationTest\Rule\Session\Action;

use Application\Rule\Session\Action\WriteValueToSession;
use PHPUnit\Framework\TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;

/**
 * Test WriteValueToSessionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WriteValueToSessionTest extends TestCase
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
        $this->container = new Container('TEST', new SessionManager(null, new ArrayStorage()));
    }

    /**
     * @test
     */
    public function testItShouldWriteValueToSession()
    {
        $action = new WriteValueToSession(
            $this->container,
            'foo',
            'bar'
        );

        $this->assertFalse(
            $this->container->offsetExists('foo'),
            WriteValueToSession::class . ' cannot test since the container has information set'
        );

        $action(new BasicRuleItem());

        $this->assertEquals(
            'bar',
            $this->container->offsetGet('foo'),
            WriteValueToSession::class . ' did not write to the container'
        );
    }
}
