<?php

namespace ApiTest\V1\Rest\Game;

use Api\V1\Rest\Game\GameEntity;
use Game\Game;
use \PHPUnit\Framework\TestCase as TestCase;

/**
 * Test GameEntityTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GameEntityTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSetLinksFromGame()
    {
        $date       = new \DateTime();
        $gameEntity = new GameEntity([
            'game_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts',
            'meta'        => ['desktop' => 'false', 'unity' => 'false'],
            'created'     => $date,
            'updated'     => $date,
            'deleted'     => null,
            'flags'       => Game::GAME_FEATURED + Game::GAME_COMING_SOON + Game::GAME_GLOBAL,
            'uris'        => [
                Game::URL_THUMB  =>
                    'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                Game::URL_BANNER =>
                    'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                Game::URL_GAME   => 'https://games.changemyworldnow.com/sea-turtle',
            ],
        ]);

        $this->assertEquals(
            'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
            $gameEntity->getLinks()->get(Game::URL_BANNER)->getUrl(),
            GameEntity::class . ' did not add the correct banner thumb link from the array'
        );

        $this->assertEquals(
            'https://games.changemyworldnow.com/sea-turtle',
            $gameEntity->getLinks()->get(Game::URL_GAME)->getUrl(),
            GameEntity::class . ' did not add the game link from the array'
        );

        $this->assertEquals(
            'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
            $gameEntity->getLinks()->get(Game::URL_THUMB)->getUrl(),
            GameEntity::class . ' did not add the thumb link from the array'
        );
    }

    /**
     * @test
     */
    public function testItShouldSetPropertiesFromGetArrayCopy()
    {
        $date       = new \DateTime();
        $gameEntity = new GameEntity([
            'game_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts',
            'meta'        => ['desktop' => 'false', 'unity' => 'false'],
            'created'     => $date,
            'updated'     => $date,
            'deleted'     => $date,
            'flags'       => Game::GAME_FEATURED + Game::GAME_COMING_SOON + Game::GAME_GLOBAL,
            'uris'        => [
                Game::URL_THUMB  =>
                    'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                Game::URL_BANNER =>
                    'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                Game::URL_GAME   => 'https://games.changemyworldnow.com/sea-turtle',
            ],
        ]);

        $actual = $gameEntity->getArrayCopy();

        $this->assertArrayNotHasKey(
            'uris',
            $actual,
            GameEntity::class . ' did not remove the uris from the array'
        );

        $this->assertArrayNotHasKey(
            'flags',
            $actual,
            GameEntity::class . ' did not remove the flags from the array'
        );

        // check that the dates are strings
        $this->assertTrue(
            is_string($actual['created']),
            GameEntity::class . ' did not cast created date to a string'
        );

        $this->assertTrue(
            is_string($actual['updated']),
            GameEntity::class . ' did not cast updated date to a string'
        );

        $this->assertTrue(
            is_string($actual['deleted']),
            GameEntity::class . ' did not cast delete date to a string'
        );

        // Check the flags are now properties
        $this->assertTrue(
            $actual['coming_soon'],
            GameEntity::class . ' did not transform the coming soon flag to a property'
        );

        $this->assertTrue(
            $actual['global'],
            GameEntity::class . ' did not transform the global flag to a property'
        );

        $this->assertTrue(
            $actual['featured'],
            GameEntity::class . ' did not transform the featured flag to a property'
        );
    }
}
