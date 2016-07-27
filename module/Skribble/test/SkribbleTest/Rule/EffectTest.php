<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\Effect;

/**
 * Test EffectTest
 *
 * @group Skribble
 * @group SkribbleRule
 * @group Media
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class EffectTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateCorrectly()
    {
        $expected = [
            'media_id'   => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type' => 'effect',
            'check'      => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'  => 'application/javascript',
            'src'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'thumb'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'order'       => 1,
            'name'       => 'Explosions',
            'type'       => 'file',
            'can_overlap' => false,
        ];

        $effect = new Effect($expected);
        $this->assertEquals(
            $expected,
            $effect->getArrayCopy(),
            'Things are always better with explosions, exception this did not explode'
        );

        $this->assertTrue($effect->isValid(), 'Effect is not reporting valid state');
        $this->assertEquals('effect', $effect->getRuleType());
    }

    /**
     * @test
     */
    public function testItShouldBeAbleToHydrateFromItself()
    {
        $expected = new Effect([
            'media_id'   => '82dd5620-df30-11e5-a52e-0800274877349',
            'asset_type' => 'effect',
            'check'      => [
                'type'  => 'sha1',
                'value' => '82dd5620df3011e5a52e0800274877349',
            ],
            'mime_type'  => 'application/javascript',
            'src'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'thumb'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
            'order'       => 1,
            'name'       => 'Explosions',
            'type'       => 'file',
            'can_overlap' => false,
        ]);

        $actual = new Effect($expected->getArrayCopy());

        $this->assertEquals($expected, $actual, 'Effect cannot hydrate from itself');
    }
}
