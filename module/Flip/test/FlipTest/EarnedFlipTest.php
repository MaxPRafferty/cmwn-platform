<?php

namespace FlipTest;

use Flip\EarnedFlip;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test EarnedFlipTest
 *
 * @group User
 * @group Flip
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
            'flip_id'        => 'foo-bar',
            'title'          => 'Manchuck Flip',
            'description'    => 'The Best Flip to earn',
            'earned'         => $expectedDate,
            'earned_by'      => 'manchuck',
            'acknowledge_id' => 'acknowledge_me',

        ];

        $flip = new EarnedFlip($expectedData);

        $expectedData['earned'] = $expectedDate->format(\DateTime::ISO8601);
        $this->assertEquals(
            $expectedData,
            $flip->getArrayCopy(),
            EarnedFlip::class . ' did not hydrate properly'
        );
    }

    /**
     * @test
     */
    public function testItShouldHydrateAndExtractWithDateString()
    {
        $expectedDate = new \DateTime('1982-05-13 23:43:00');
        $expectedData = [
            'flip_id'        => 'foo-bar',
            'title'          => 'Manchuck Flip',
            'description'    => 'The Best Flip to earn',
            'earned'         => $expectedDate->format(\DateTime::ISO8601),
            'earned_by'      => 'manchuck',
            'acknowledge_id' => 'acknowledge_me',
        ];

        $flip = new EarnedFlip($expectedData);

        $expectedData['earned'] = $expectedDate->format(\DateTime::ISO8601);
        $this->assertEquals(
            $expectedData,
            $flip->getArrayCopy(),
            EarnedFlip::class . ' did not hydrate date string properly'
        );
    }

    /**
     * @test
     * @ticket CORE-2297
     */
    public function testItShouldReportAcknowledgementCorrectly()
    {
        $flip = new EarnedFlip();
        $this->assertTrue(
            $flip->isAcknowledged(),
            EarnedFlip::class . ' falsely reported it was Acknowledged'
        );

        $flip->setAcknowledgeId('acknowledge_me');
        $this->assertFalse(
            $flip->isAcknowledged(),
            EarnedFlip::class . ' reported it was Acknowledged with an Acknowledge Id set'
        );
    }
}
