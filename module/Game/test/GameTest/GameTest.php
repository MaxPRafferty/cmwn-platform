<?php

namespace GameTest;

use Game\Game;
use PHPUnit\Framework\TestCase;
use Zend\Json\Json;

/**
 * Tests the game model
 */
class GameTest extends TestCase
{
    /**
     * Tests the data is hydrated to the game when passed data
     */
    public function testItShouldHydrateData()
    {
        $date     = new \DateTime();
        $expected = [
            'game_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts',
            'meta'        => ['desktop' => 'false', 'unity' => 'false'],
            'created'     => $date->format('Y-m-d H:i:s'),
            'updated'     => $date->format('Y-m-d H:i:s'),
            'deleted'     => null,
            'flags'       => Game::GAME_FEATURED
                + Game::GAME_COMING_SOON
                + Game::GAME_GLOBAL
                + Game::GAME_DESKTOP
                + Game::GAME_UNITY,
            'uris'        => [
                Game::URL_THUMB  =>
                    'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                Game::URL_BANNER =>
                    'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                Game::URL_GAME   => 'https://games.changemyworldnow.com/sea-turtle',
            ],
            'sort_order'  => 2,
        ];

        $game = new Game();
        $game->exchangeArray($expected);

        $expected['coming_soon'] = true;
        $expected['global']      = true;
        $expected['featured']    = true;
        $expected['desktop']     = true;
        $expected['unity']       = true;
        unset($expected['flags']);
        $this->assertEquals(
            $expected,
            $game->getArrayCopy(),
            Game::class . ' was not hydrated correctly'
        );
    }

    /**
     * Tests the data is hydrated to the game when passed data
     */
    public function testItShouldHydrateDataFromDbData()
    {
        $date     = new \DateTime();
        $expected = [
            'game_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts',
            'meta'        => ['desktop' => 'false', 'unity' => 'false'],
            'created'     => $date->format('Y-m-d H:i:s'),
            'updated'     => $date->format('Y-m-d H:i:s'),
            'deleted'     => null,
            'flags'       => Game::GAME_FEATURED
                + Game::GAME_COMING_SOON
                + Game::GAME_GLOBAL
                + Game::GAME_DESKTOP
                + Game::GAME_UNITY,
            'uris'        => Json::encode([
                Game::URL_THUMB  =>
                    'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                Game::URL_BANNER =>
                    'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                Game::URL_GAME   => 'https://games.changemyworldnow.com/sea-turtle',
            ]),
            'sort_order'  => 2,
        ];

        $game = new Game();
        $game->exchangeArray($expected);

        $expected['coming_soon'] = true;
        $expected['global']      = true;
        $expected['featured']    = true;
        $expected['desktop']     = true;
        $expected['unity']       = true;
        $expected['uris']        = Json::decode($expected['uris'], Json::TYPE_ARRAY);
        unset($expected['flags']);
        $this->assertEquals(
            $expected,
            $game->getArrayCopy(),
            Game::class . ' was not hydrated correctly using DB data'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateDefaultGameIdWhenNotSet()
    {
        $game = new Game();
        $this->assertNotEmpty(
            $game->getGameId(),
            Game::class . ' did not create a UUID when asked for the game Id'
        );

        $game->setGameId('foo-bar');
        $this->assertEquals(
            'foo-bar',
            $game->getGameId(),
            Game::class . ' did not allow a custom Id to be set'
        );
    }

    /**
     * @test
     */
    public function testItShouldSetComingSoonCorrectly()
    {
        $game = new Game();
        $this->assertFalse(
            $game->isComingSoon(),
            Game::class . ' initialized as coming soon by default'
        );

        $this->assertFalse(
            $game->isGlobal(),
            Game::class . ' initialized as global by default'
        );

        $this->assertFalse(
            $game->isFeatured(),
            Game::class . ' initialized as featured by default'
        );

        $game->toggleFlag(Game::GAME_COMING_SOON);

        $this->assertTrue(
            $game->isComingSoon(),
            Game::class . ' did not toggle coming soon on'
        );

        $this->assertFalse(
            $game->isGlobal(),
            Game::class . ' also toggled global along with coming soon '
        );

        $this->assertFalse(
            $game->isFeatured(),
            Game::class . ' also toggled featured along with coming soon '
        );
    }

    /**
     * @test
     */
    public function testItShouldSetFeaturedCorrectly()
    {
        $game = new Game();
        $this->assertFalse(
            $game->isComingSoon(),
            Game::class . ' initialized as coming soon by default'
        );

        $this->assertFalse(
            $game->isGlobal(),
            Game::class . ' initialized as global by default'
        );

        $this->assertFalse(
            $game->isFeatured(),
            Game::class . ' initialized as featured by default'
        );

        $game->toggleFlag(Game::GAME_FEATURED);

        $this->assertFalse(
            $game->isComingSoon(),
            Game::class . ' also toggled global along with featured '
        );

        $this->assertFalse(
            $game->isGlobal(),
            Game::class . ' also toggled global along with featured '
        );

        $this->assertTrue(
            $game->isFeatured(),
            Game::class . ' did not toggle featured '
        );
    }

    /**
     * @test
     */
    public function testItShouldSetGlobalCorrectly()
    {
        $game = new Game();
        $this->assertFalse(
            $game->isComingSoon(),
            Game::class . ' initialized as coming soon by default'
        );

        $this->assertFalse(
            $game->isGlobal(),
            Game::class . ' initialized as global by default'
        );

        $this->assertFalse(
            $game->isFeatured(),
            Game::class . ' initialized as featured by default'
        );

        $game->toggleFlag(Game::GAME_GLOBAL);

        $this->assertFalse(
            $game->isComingSoon(),
            Game::class . ' also toggled global along with global '
        );

        $this->assertTrue(
            $game->isGlobal(),
            Game::class . ' did not toggle global '
        );

        $this->assertFalse(
            $game->isFeatured(),
            Game::class . ' also toggled featured along with global '
        );
    }

    /**
     * @test
     */
    public function testItShouldSetMultipleFlagsCorrectly()
    {
        $game = new Game();
        // By this point we know that the flags are not set

        $game->toggleFlag(Game::GAME_GLOBAL);
        $game->toggleFlag(Game::GAME_FEATURED);

        $this->assertTrue(
            $game->isFeatured(),
            Game::class . ' did not set featured with global'
        );

        $this->assertTrue(
            $game->isGlobal(),
            Game::class . ' did not set global with featured'
        );

        $this->assertFalse(
            $game->isComingSoon(),
            Game::class . ' also turned on coming soon with global and featured'
        );

        $game->toggleFlag(Game::GAME_FEATURED);
        $game->toggleFlag(Game::GAME_COMING_SOON);

        $this->assertTrue(
            $game->isComingSoon(),
            Game::class . ' did not toggle coming soon after toggeling featured'
        );

        $this->assertFalse(
            $game->isFeatured(),
            Game::class . ' did not toggle off featured'
        );
    }

    /**
     * @test
     */
    public function testItShouldToggleFlagsFromNumbers()
    {
        $game = new Game();

        $game->setFlags(Game::GAME_COMING_SOON + Game::GAME_FEATURED);

        $this->assertTrue(
            $game->isComingSoon(),
            Game::class . ' did not set coming soon from a number'
        );

        $this->assertTrue(
            $game->isFeatured(),
            Game::class . ' did not set featured from a number'
        );

        $this->assertFalse(
            $game->isGlobal(),
            Game::class . ' also set global flag when setting flags by number'
        );
    }

    /**
     * @test
     */
    public function testItShouldSetUrisCorrectly()
    {
        $game = new Game();
        $this->assertEmpty(
            $game->getUri(Game::URL_BANNER),
            Game::class . ' did not default banner url to be empty'
        );

        $this->assertEmpty(
            $game->getUri(Game::URL_GAME),
            Game::class . ' did not default game url to be empty'
        );

        $this->assertEmpty(
            $game->getUri(Game::URL_THUMB),
            Game::class . ' did not default thumb url to be empty'
        );

        $game->addUri(
            Game::URL_BANNER,
            'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg'
        );

        $game->addUri(
            Game::URL_THUMB,
            'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg'
        );

        $game->addUri(
            Game::URL_GAME,
            'https://games.changemyworldnow.com/sea-turtle'
        );

        $this->assertEquals(
            'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
            $game->getUri(Game::URL_BANNER),
            Game::class . ' did not set the banner url'
        );

        $this->assertEquals(
            'https://games.changemyworldnow.com/sea-turtle',
            $game->getUri(Game::URL_GAME),
            Game::class . ' did not set the thumb url'
        );

        $this->assertEquals(
            'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
            $game->getUri(Game::URL_THUMB),
            Game::class . ' did not set the game url'
        );
    }

    /**
     * @test
     */
    public function testItShouldTakeFlagsInArrayAndSetThem()
    {
        $date     = new \DateTime();
        $expected = [
            'game_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts',
            'meta'        => ['desktop' => 'false', 'unity' => 'false'],
            'created'     => $date->format('Y-m-d H:i:s'),
            'updated'     => $date->format('Y-m-d H:i:s'),
            'deleted'     => null,
            // These are here to ensure that the flags are set from the flags prop
            'coming_soon' => false,
            'global'      => false,
            'featured'    => false,
            'unity'       => false,
            'desktop'     => false,
            'uris'        => [
                Game::URL_THUMB  =>
                    'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                Game::URL_BANNER =>
                    'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                Game::URL_GAME   => 'https://games.changemyworldnow.com/sea-turtle',
            ],
            'sort_order'  => 2,
        ];

        $game = new Game();
        // we want to make sure the keys overwrite the flag
        $game->setFlags(
            Game::GAME_FEATURED
            + Game::GAME_COMING_SOON
            + Game::GAME_GLOBAL
            + Game::GAME_DESKTOP
            + Game::GAME_UNITY
        );

        $game->exchangeArray($expected);

        unset($expected['flags']);
        $this->assertEquals(
            $expected,
            $game->getArrayCopy(),
            Game::class . ' was not hydrated correctly with flags as keys '
        );
    }
}
