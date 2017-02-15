<?php

namespace MediaTest;

use Media\Media;
use PHPUnit\Framework\TestCase as TestCase;
use Zend\Json\Json;

/**
 * Test MediaTest
 *
 * @group Media
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class MediaTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateFromJsonDataCorrectly()
    {
        $json = '{
            "type": "file",
            "media_id": "70116569037",
            "name": "img_animals_sprite.png",
            "can_overlap": true,
            "asset_type": "background",
            "check": {
                "type": "sha1",
                "value": "da39a3ee5e6b4b0d3255bfef95601890afd80709"
            },
            "src": "https://media.changemyworldnow.com/f/70116569037",
            "thumb": "https://media.changemyworldnow.com/f/70116569037",
            "order": 1,
            "mime_type": "image/png",
            "name": "A Cool Image"
        }';

        $media    = new Media(Json::decode($json, Json::TYPE_ARRAY));
        $expected = [
            'media_id'    => '70116569037',
            'asset_type'  => 'background',
            'can_overlap' => true,
            'check'       => [
                'type'  => 'sha1',
                'value' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
            ],
            'mime_type'   => 'image/png',
            'src'         => 'https://media.changemyworldnow.com/f/70116569037',
            'thumb'       => 'https://media.changemyworldnow.com/f/70116569037',
            'order'       => 1,
            'name'        => 'A Cool Image',
            'type'        => 'file',
        ];

        $this->assertEquals(
            $expected,
            $media->getArrayCopy(),
            'Did not extract media_id correctly from json'
        );
    }

    /**
     * @test
     */
    public function testItShouldHydrateFromFlatArray()
    {
        $expected = [
            'media_id'    => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type'  => 'background',
            'can_overlap' => true,
            'check'       => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'   => 'image/png',
            'src'         => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'thumb'       => 'https://media.changemyworldnow.com/f/70116569037',
            'order'       => 1,
            'name'        => 'A Cool Image',
            'type'        => 'file',
        ];

        $media = new Media($expected);

        $this->assertEquals($expected, $media->getArrayCopy(), 'Media did not hydrate from flat array');
    }
}
