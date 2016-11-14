<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\Item;

/**
 * Test ItemTest
 *
 * @group Skribble
 * @group SkribbleRule
 * @group Media
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ItemTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateCorrectly()
    {
        $expected = [
            'media_id'   => '70116576425',
            'name'       => 'img_dogs_1-01.png',
            'asset_type' => 'item',
            'check'      => [
                'type'  => 'sha1',
                'value' => '05735b54100ea1c1054da594550792c4f1c36fc5',
            ],
            'src'        => 'https://media.changemyworldnow.com/f/70116576425',
            'thumb'      => 'https://media.changemyworldnow.com/f/70116576425',
            'order'      => 1,
            'mime_type'  => 'image/png',
            'type'       => 'file',
            'can_overlap' => false,
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
        ];

        $item = new Item($expected);

        $this->assertEquals(
            $expected,
            $item->getArrayCopy(),
            'Item did not hydrate correctly'
        );

        $this->assertTrue($item->isValid(), 'Item is not reporting valid');
        $this->assertTrue($item->isStateValid(), 'Item is not reporting valid state');
        $this->assertEquals('item', $item->getRuleType());
    }

    /**
     * @test
     */
    public function testItShouldBeAbleToHydrateItSelf()
    {
        $expected = new Item([
            'media_id'   => '70116576425',
            'name'       => 'img_dogs_1-01.png',
            'asset_type' => 'item',
            'check'      => [
                'type'  => 'sha1',
                'value' => '05735b54100ea1c1054da594550792c4f1c36fc5',
            ],
            'src'        => 'https://media.changemyworldnow.com/f/70116576425',
            'thumb'      => 'https://media.changemyworldnow.com/f/70116576425',
            'order'      => 1,
            'mime_type'  => 'image/png',
            'type'       => 'file',
            'can_overlap' => false,
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
        ]);

        $actual = new Item($expected->getArrayCopy());
        $this->assertEquals($expected, $actual, 'Item cannot hydrate itself');
    }
}
