<?php

namespace ApplicationTest\Rule\Session\Provider;

use Application\Rule\Session\Provider\SessionValue;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;

/**
 * Test SessionValueTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SessionValueTest extends TestCase
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
    public function testItShouldReturnValueFromContainerWithExplicitName()
    {
        $this->container->offsetSet('foo', 'bar');
        $provider = new SessionValue($this->container, 'foo', 'buzz');

        $this->assertEquals(
            'buzz',
            $provider->getName(),
            SessionValue::class . ' did not set the correct name it provides'
        );

        $this->assertEquals(
            'bar',
            $provider->getValue(),
            SessionValue::class . ' did not return the correct value'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnValueFromContainerUsingContainerName()
    {
        $this->container->offsetSet('foo', 'bar');
        $provider = new SessionValue($this->container, 'foo');

        $this->assertEquals(
            'foo',
            $provider->getName(),
            SessionValue::class . ' did not set the correct name it provides'
        );

        $this->assertEquals(
            'bar',
            $provider->getValue(),
            SessionValue::class . ' did not return the correct value'
        );
    }
}
