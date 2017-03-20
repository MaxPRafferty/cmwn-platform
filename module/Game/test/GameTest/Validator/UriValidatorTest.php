<?php

namespace GameTest\Validator;

use Game\Validator\UriValidator;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test UriValidatorTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UriValidatorTest extends TestCase
{

    /**
     * @test
     */
    public function testItShouldValidateWithAGoodValue()
    {
        $validator = new UriValidator();
        $this->assertTrue(
            $validator->isValid([
                'thumb_url'  =>
                    'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                'banner_url' =>
                    'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
            ]),
            UriValidator::class . ' failed to validate a positive value'
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenValueIsNotAString()
    {
        $validator = new UriValidator();
        $this->assertFalse(
            $validator->isValid('not a uri'),
            UriValidator::class . ' did not fail when an array is not passed in'
        );

        $this->assertEquals(
            ['invalidType' => 'Value passed in is not a key value pair'],
            $validator->getMessages(),
            UriValidator::class . ' is reporting invalid messages on a string value'
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenValueIsMissingFields()
    {
        $validator = new UriValidator();
        $this->assertFalse(
            $validator->isValid(['thumb_url' => 'not a uri']),
            UriValidator::class . ' did not fail when an array is not passed in'
        );

        $this->assertEquals(
            ['missingKey' => 'Missing keys or invalid key set expected: [thumb_url, banner_url, game_url]'],
            $validator->getMessages(),
            UriValidator::class . ' is reporting invalid messages on a string value'
        );
    }

    /**
     * @test
     * @dataProvider invalidGameUris
     */
    public function testItShouldFailWhenOneOrMoreKeysAreNotAURI($value)
    {
        $validator = new UriValidator();

        $this->assertFalse(
            $validator->isValid($value),
            UriValidator::class . ' did not fail when an array is not passed in'
        );
    }

    public function invalidGameUris()
    {
        return [
            'Thumb Invalid' => [
                [
                    'thumb_url'  => 'not a uri',
                    'banner_url' =>
                        'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                    'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                ],
            ],

            'Missing Scheme' => [
                [
                    'thumb_url'  => 'www.google.com',
                    'banner_url' =>
                        'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                    'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                ],
            ],

            'Not Secure Scheme' => [
                [
                    'thumb_url'  => 'http://www.google.com',
                    'banner_url' =>
                        'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                    'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                ],
            ],
        ];
    }
}
