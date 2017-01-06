<?php

namespace IntegrationTest;

use \PHPUnit_Framework_TestCase as TestCase;
use ZF\Apigility\Application;

/**
 * Test InitTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InitTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldInitTheApplication()
    {
        $this->assertNotNull(
            Application::init(TestHelper::getApplicationConfig())
        );
    }
}
