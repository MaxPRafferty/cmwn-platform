<?php

namespace FlipTest;

use Flip\EarnedFlip;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test EarnedFlipTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class EarnedFlipTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateAndExtractProperlyWithDate()
    {
        $expectedDate = new \DateTime('1982-05-13 23:43:00');
        $expectedData = [
            'flip_id'     => 'foo-bar',
            'title'       => 'Manchuck Flip',
            'description' => 'The Best Flip to earn',
            'earned'      => $expectedDate,
        ];

        $flip = new EarnedFlip($expectedData);

        $expectedData['earned'] = $expectedDate->format(\DateTime::ISO8601);
        $this->assertEquals($expectedData, $flip->getArrayCopy(), 'Flip did not hydrate properly');
    }

    /**
     * @test
     */
    public function testItShouldHydrateAndExtractWithDateString()
    {
        $expectedDate = new \DateTime('1982-05-13 23:43:00');
        $expectedData = [
            'flip_id'     => 'foo-bar',
            'title'       => 'Manchuck Flip',
            'description' => 'The Best Flip to earn',
            'earned'      => $expectedDate->format(\DateTime::ISO8601),
        ];

        $flip = new EarnedFlip($expectedData);

        $expectedData['earned'] = $expectedDate->format(\DateTime::ISO8601);
        $this->assertEquals($expectedData, $flip->getArrayCopy(), 'Flip did not hydrate properly');
    }
}
