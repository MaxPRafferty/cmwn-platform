<?php

namespace SecurityTest\Guard;

use IntegrationTest\SessionManager;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;

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
    /**
     * @var SessionManager
     */
    protected $manager;

    /**
     * @var Container
     */
    protected $container;

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
     * @test
     */
    public function testItShouldNotFail()
    {
        $this->markTestIncomplete('Not Implemented');
    }
}
