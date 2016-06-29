<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\Item;

/**
 * Test ItemTest
 *
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
            'type'        => 'item',
            'media_id'    => '70116576425',
            'name'        => 'img_dogs_1-01.png',
            'asset_type'  => 'item',
            'check'       => [
                'type'  => 'sha1',
                'value' => '05735b54100ea1c1054da594550792c4f1c36fc5',
            ],
            'src'         => 'https://media.changemyworldnow.com/f/70116576425',
            'mime_type'   => 'image/png',
            'state'       => [
                'left'     => '100.831257078142695',
                'top'      => '-200.23782559456399',
                'scale'    => '0.2558923622667492',
                'rotation' => '90',
                'layer'    => '1000',
                'valid'    => true,
                'corners'  => [
                    [
                        'x' => '542.3203059418945',
                        'y' => '474.9137374254731',
                    ],
                    [
                        'x' => '542.3203059418945',
                        'y' => '244.61061138539884',
                    ],
                    [
                        'x' => '312.0171799018201',
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
        $this->assertEquals('item', $item->getType());
    }
}
