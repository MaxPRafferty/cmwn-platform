<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\Background;
use Skribble\Rule\Effect;
use Skribble\Rule\Item;
use Skribble\Rule\Message;
use Skribble\Rule\RuleStaticFactory;
use Skribble\Rule\Sound;

/**
 * Test RuleStaticFactoryTest
 *
 * @group Skribble
 * @group SkirbbleRule
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RuleStaticFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider ruleDataProvider
     */
    public function testItShouldCreateRuleFromArray($data, $expectedInstance)
    {
        $rule = RuleStaticFactory::createRuleFromArray($data);

        $this->assertInstanceOf(
            $expectedInstance,
            $rule,
            'The rule was not created from the array'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTypeNotInArray()
    {
        $this->setExpectedException(\RuntimeException::class);
        RuleStaticFactory::createRuleFromArray([]);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTypeNotARule()
    {
        $this->setExpectedException(\RuntimeException::class);
        RuleStaticFactory::createRuleFromArray(['type' => \stdClass::class]);
    }

    /**
     * @return array
     */
    public function ruleDataProvider()
    {
        return [
            'Background' => [
                'data'             => [
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
                ],
                'expectedInstance' => Background::class,
            ],

            'Effect' => [
                'data'             => [
                    'media_id'   => '82dd5620-df30-11e5-a52e-0800274877349',
                    'asset_type' => 'effect',
                    'check'      => [
                        'type'  => 'sha1',
                        'value' => '82dd5620df3011e5a52e0800274877349',
                    ],
                    'mime_type'  => 'application/javascript',
                    'src'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
                    'name'       => 'Explosions',
                    'type'       => 'effect',
                ],
                'expectedInstance' => Effect::class,
            ],

            'Item' => [
                'data'             => [
                    'media_id'   => '70116576425',
                    'name'       => 'img_dogs_1-01.png',
                    'asset_type' => 'item',
                    'check'      => [
                        'type'  => 'sha1',
                        'value' => '05735b54100ea1c1054da594550792c4f1c36fc5',
                    ],
                    'src'        => 'https://media.changemyworldnow.com/f/70116576425',
                    'mime_type'  => 'image/png',
                    'type'       => 'item',
                    'state'      => [
                        'left'     => '100.83125707814269',
                        'top'      => '-200.2378255945639',
                        'scale'    => '0.25589236226674',
                        'rotation' => '90',
                        'layer'    => '1000',
                        'valid'    => true,
                        'corners'  => [
                            [
                                'x' => '542.32030594189453',
                                'y' => '474.91373742547311',
                            ],
                            [
                                'x' => '542.32030594189453',
                                'y' => '244.61061138539884',
                            ],
                            [
                                'x' => '312.01717990182011',
                                'y' => '244.61061138539884',
                            ],
                            [
                                'x' => '312.01717990182016',
                                'y' => '474.91373742547313',
                            ],
                        ],
                    ],
                ],
                'expectedInstance' => Item::class,
            ],

            'Message' => [
                'data'             => [
                    'media_id'   => '70116576425',
                    'name'       => 'img_dogs_1-01.png',
                    'asset_type' => 'message',
                    'check'      => [
                        'type'  => 'sha1',
                        'value' => '05735b54100ea1c1054da594550792c4f1c36fc5',
                    ],
                    'src'        => 'https://media.changemyworldnow.com/f/70116576425',
                    'mime_type'  => 'image/png',
                    'type'       => 'message',
                    'state'      => [
                        'left'     => '100.83125707814269',
                        'top'      => '-200.2378255945639',
                        'scale'    => '0.25589236226674',
                        'rotation' => '90',
                        'layer'    => '1000',
                        'valid'    => true,
                        'corners'  => [
                            [
                                'x' => '542.32030594189453',
                                'y' => '474.91373742547311',
                            ],
                            [
                                'x' => '542.32030594189453',
                                'y' => '244.61061138539884',
                            ],
                            [
                                'x' => '312.01717990182011',
                                'y' => '244.61061138539884',
                            ],
                            [
                                'x' => '312.01717990182016',
                                'y' => '474.91373742547313',
                            ],
                        ],
                    ],
                ],
                'expectedInstance' => Message::class,
            ],

            'Sound' => [
                'data'             => [
                    'media_id'   => '82dd5620-df30-11e5-a52e-0800274877349',
                    'asset_type' => 'sound',
                    'check'      => [
                        'type'  => 'sha1',
                        'value' => '82dd5620df3011e5a52e0800274877349',
                    ],
                    'mime_type'  => 'audo/mpeg',
                    'src'        => 'https://media.changemyworldnow.com/f/82dd5620-df30-11e5-a52e-0800274877349',
                    'name'       => 'My Cool Sound',
                    'type'       => 'sound',
                ],
                'expectedInstance' => Sound::class,
            ],
        ];
    }
}
