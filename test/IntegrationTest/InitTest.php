<?php

namespace IntegrationTest;

use PHPUnit\Framework\TestCase as TestCase;
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
     * This checks that application can be bootstrapped
     *
     * I run this test with the networked services off to ensure that no module is
     * trying to connect at the time of bootstrapping
     *
     * @test
     */
    public function testItShouldInitTheApplication()
    {
        $this->assertNotNull(
            Application::init(TestHelper::getApplicationConfig())
        );
    }
}
