<?php

namespace GameTest;

use Application\Utils\Date\DateTimeFactory;
use Game\Exception\RuntimeException;
use Game\Game;
use Game\SaveGame;
use PHPUnit\Framework\TestCase;
use User\Child;

/**
 * Test SaveGameTest
 */
class SaveGameTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldTakeArrayInConstructor()
    {
        $date = DateTimeFactory::factory('now');

        $data = [
            'game_id' => 'monarch',
            'user_id' => 'manchuck',
            'data'    => ['foo' => 'bar', 'progress' => 100],
            'created' => $date->format("Y-m-d H:i:s"),
            'version' => '1.1.1',
        ];

        $saveGame = new SaveGame($data);

        $this->assertEquals(
            $data,
            $saveGame->getArrayCopy(),
            SaveGame::class . ' did not set the data from the constructor correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldHaveHelpersForSettingIds()
    {
        $user = new Child();
        $user->setUserId('manchuck');

        $game = new Game();
        $game->setGameId('monarch');

        $save = new SaveGame();
        $save->setGameIdFromGame($game);
        $save->setUserIdFromUser($user);

        $this->assertEquals(
            'manchuck',
            $save->getUserId(),
            SaveGame::class . ' did not set the user id from a user'
        );

        $this->assertEquals(
            'monarch',
            $save->getGameId(),
            SaveGame::class . ' did not set the game id from a game'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenDataIsNotAnArray()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data for game MUST be an array or Json string');

        $save = new SaveGame();
        $save->setData(new \stdClass());
    }
}
