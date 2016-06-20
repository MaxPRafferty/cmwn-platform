<?php

namespace GameTest;

use Game\SaveGame;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test SaveGameTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SaveGameTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldTakeArrayInConstructor()
    {
        $date = new \DateTime();

        $data = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => '1.1.1',
        ];

        $saveGame = new SaveGame($data);

        $this->assertEquals('monarch', $saveGame->getGameId(), 'Game Id was not set from constructor');
        $this->assertEquals('manchuck', $saveGame->getUserId(), 'User Id was not set from constructor');
        $this->assertEquals($date, $saveGame->getCreated(), 'Crated date was not set from constructor');
        $this->assertEquals(
            ['foo' => 'bar', 'progress' => 100],
            $saveGame->getData(),
            'Game Data was not set from constructor'
        );

        $this->assertEquals('1.1.1', $saveGame->getVersion(), 'The version is incorrect');
    }

    /**
     * @test
     */
    public function testItShouldBeAbleToExtractAndHydrateItself()
    {
        $date = new \DateTime();

        $data = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format(\DateTime::ISO8601),
            'version' => '3.3.3-rc',
        ];

        $expected = new SaveGame($data);
        $actual   = new SaveGame();

        $this->assertEquals(
            $data,
            $expected->getArrayCopy(),
            'Save Game was not able to correctly extract itself'
        );

        $actual->exchangeArray($expected->getArrayCopy());
        $this->assertEquals($expected, $actual, 'Hydrating into new SaveGame produced incorrect data');
    }
}
