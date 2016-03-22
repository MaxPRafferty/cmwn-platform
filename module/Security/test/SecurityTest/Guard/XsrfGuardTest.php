<?php

namespace SecurityTest\Guard;

use IntegrationTest\SessionManager;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Container;

/**
 * Exception XsrfGuardTest
 *
 * ${CARET}
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
        $_SESSION = [];
        Container::setDefaultManager(null);
        $config = new StandardConfig([
            'storage' => 'Zend\\Session\\Storage\\ArrayStorage',
        ]);

        $this->manager   = $manager = new SessionManager($config);
        $this->container = new Container('Default', $manager);
    }

    public function testItShouldNotFail()
    {
        $this->assertTrue(true);
    }
}
