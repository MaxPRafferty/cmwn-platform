<?php

namespace SkribbleTest\Rule;

use PHPUnit\Framework\TestCase as TestCase;
use Skribble\Rule\Sound;

/**
 * Test SoundTest
 *
 * @group Skribble
 * @group SkribbleRule
 * @group Media
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SoundTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateCorrectly()
    {
        $expected = [
            'media_id'    => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type'  => 'sound',
            'check'       => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'   => 'audo/mpeg',
            'src'         => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'thumb'       => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'order'       => 1,
            'name'        => 'My Cool Sound',
            'type'        => 'file',
            'can_overlap' => false,
        ];

        $sound = new Sound($expected);
        $this->assertEquals(
            $expected,
            $sound->getArrayCopy(),
            'Sound asset was not hydrated correctly'
        );

        $this->assertTrue($sound->isValid(), 'Sound should default to valid');
        $this->assertEquals('sound', $sound->getRuleType(), 'Sound is reporting incorrect type');
    }

    /**
     * @test
     */
    public function testItShouldExtractAndHydrateFromItself()
    {
        $expected = new Sound([
            'media_id'    => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type'  => 'sound',
            'check'       => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'   => 'audo/mpeg',
            'src'         => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'thumb'       => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'order'       => 1,
            'name'        => 'My Cool Sound',
            'type'        => 'sound',
            'can_overlap' => false,
        ]);

        $actual = new Sound($expected->getArrayCopy());
        $this->assertEquals($expected, $actual, 'Sound cannot hydrate from itself');
    }
}
