<?php

namespace IntegrationTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Client;

/**
 * Test ClientTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldDownloadSomething()
    {
        /** @var Client $client */
        $client = TestHelper::getServiceManager()->get(Client::class);

        $client->setUri(
            'http://pre05.deviantart.net/9a29/th/pre/f/2011/163/5/d/bulbasaur__old__by_blastertwo-d3ir8m6.png'
        );

        $client->setStream('/var/www/tmp/testFile.png');
        $client->send();
    }
}
