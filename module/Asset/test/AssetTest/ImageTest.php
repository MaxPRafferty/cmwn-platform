<?php

namespace AssetTest;

use Asset\Image;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test ImageTest
 *
 * @group Image
 * @group Asset
 */
class ImageTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $expected = [
            'image_id'     => null,
            'url'          => null,
            'is_moderated' => false,
            'type'         => null,
            'created'      => null,
            'updated'      => null,
            'deleted'      => null,
        ];

        $image = new Image();
        $image->exchangeArray($expected);
        $this->assertEquals($expected, $image->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldHydrateData()
    {
        $date = new \DateTime();

        $expected = [
            'image_id'     => 'abcdefghijklmnop',
            'url'          => 'http://www.manchuck.com',
            'is_moderated' => true,
            'type'         => 'image/png',
            'created'      => $date->format(\DateTime::ISO8601),
            'updated'      => $date->format(\DateTime::ISO8601),
            'deleted'      => null
        ];

        $image = new Image();
        $image->exchangeArray($expected);

        $this->assertEquals($expected, $image->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldTakeModerationStatusOverIsModerated()
    {
        $expected = [
            'is_moderated'      => true,
            'moderation_status' => false,
        ];

        $image = new Image();
        $image->exchangeArray($expected);

        $this->assertFalse($image->isModerated());
    }

    /**
     * @test
     */
    public function testItShouldTakeModerationStatusOverModerated()
    {
        $expected = [
            'moderated'         => true,
            'moderation_status' => false,
        ];

        $image = new Image();
        $image->exchangeArray($expected);

        $this->assertFalse($image->isModerated());
    }
}
