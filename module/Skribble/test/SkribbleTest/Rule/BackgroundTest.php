<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\Background;

/**
 * Test BackgroundTest
 *
 * @group Skribble
 * @group SkribbleRule
 * @group Media
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class BackgroundTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateCorrectly()
    {
        $expected = [
            'media_id'   => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type' => 'background',
            'check'      => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'  => 'image/png',
            'src'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'name'       => 'Happy Trees',
            'type'       => 'file',
            'can_overlap' => false,
        ];

        $bobRoss = new Background($expected);

        $this->assertEquals(
            $expected,
            $bobRoss->getArrayCopy(),
            'Bob Ross is not happy that the background did not hydrate correctly'
        );

        $this->assertTrue($bobRoss->isValid(), 'Background should be valid by default');
        $this->assertEquals('background', $bobRoss->getRuleType(), 'Background is not reporting self as a background');
    }

    /**
     * @test
     */
    public function testItShouldBeAbleToHydrateFromItself()
    {
        $expected = new Background([
            'media_id'   => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type' => 'background',
            'check'      => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'  => 'image/png',
            'src'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'name'       => 'Happy Trees',
            'type'       => 'background',
            'can_overlap' => false,
        ]);

        $actual = new Background($expected->getArrayCopy());

        $this->assertEquals($expected, $actual, 'Background cannot be hydrated from itself');
    }
}
