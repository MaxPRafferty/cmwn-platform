<?php

namespace ApplicationTest\Rule\Session\Action;

use Application\Rule\Session\Action\WriteProviderToSession;
use PHPUnit\Framework\TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;

/**
 * Test WriteProviderToSessionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WriteProviderToSessionTest extends TestCase
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
    public function testItShouldWriteTheProviderValueToSession()
    {
        $action = new WriteProviderToSession($this->container, 'foo');
        $item   = new BasicRuleItem(new BasicValueProvider('foo', 'bar'));

        $this->assertFalse(
            $this->container->offsetExists('foo'),
            WriteProviderToSession::class . ' cannot test since the container has information set'
        );

        $action($item);
        $this->assertEquals(
            'bar',
            $this->container->offsetGet('foo'),
            WriteProviderToSession::class . ' did not write to the container'
        );
    }

    /**
     * @test
     */
    public function testItShouldWriteNullWhenTheProviderIsNotSet()
    {
        $action = new WriteProviderToSession($this->container, 'foo');
        $item   = new BasicRuleItem(new BasicValueProvider('fizz', 'buzz'));

        $this->assertFalse(
            $this->container->offsetExists('foo'),
            WriteProviderToSession::class . ' cannot test since the container has information set'
        );

        $action($item);
        $this->assertNull(
            $this->container->offsetGet('foo'),
            WriteProviderToSession::class . ' did not write null to the container when the provider is missing'
        );
    }
}
