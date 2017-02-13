<?php

namespace FlipTest;

use Flip\Flip;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test FlipTest
 *
 * @group Flip
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
        $this->assertEquals(
            $expectedData,
            $flip->getArrayCopy(),
            Flip::class . ' did not hydrate properly'
        );

        $this->assertEquals(
            'Manchuck Flip',
            (string) $flip,
            Flip::class . ' cannot be converted to a string'
        );
    }
}
