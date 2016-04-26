<?php

namespace FlipTest;

use Flip\Flip;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test FlipTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlipTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldExtractAndHydrateArray()
    {
        $expectedData = [
            'flip_id'     => 'foo-bar',
            'title'       => 'Manchuck Flip',
            'description' => 'The Best Flip to earn',
        ];
        
        $flip = new Flip($expectedData);
        $this->assertEquals($expectedData, $flip->getArrayCopy(), 'Flip did not hydrate properly');
    }
}
