<?php

namespace IntegrationTest\Api\V1\Rest;

use Api\V1\Rest\Game\GameResource;
use IntegrationTest\IntegrationTest as TestCase;
use Zend\Json\Json;

/**
 * Tests the Game Resource
 */
class GameResourceTest extends TestCase
{
    /**
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/games.dataset.php');
    }

    /**
     * @inheritDoc
     */
    protected function getControllerNameForTest(): string
    {
        return 'api\v1\rest\game\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.game';
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $route
     * @param int $code
     * @param array $expected
     *
     * @dataProvider fetchAllDataProvider
     */
    public function testItShouldFetchGame($user, $route, int $code, array $expected)
    {
        $this->dispatchAuthenticatedCall($user, $route, $code);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        // Do not sort the arrays we want to make sure they match
        $this->assertEquals(
            $expected,
            $body,
            GameResource::class . ' did not return the expected body'
        );
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $gameId
     * @param int $code
     *
     * @dataProvider deleteGameProvider
     */
    public function testItShouldDeleteGame(string $login, string $gameId, int $code, bool $hard)
    {
        $this->dispatchAuthenticatedCall($login, '/game/' . $gameId, $code, 'DELETE');

        if ($code === 404) {
            return;
        }

        $this->assertEmpty(
            $this->getResponse()->getContent(),
            GameResource::class . ' did not return empty body when deleting a game'
        );

        $results = $this->getConnection()
            ->getConnection()
            ->query('SELECT * FROM games WHERE game_id = "' . $gameId . '" LIMIT 1');

        if ($hard) {
            $this->assertEquals(
                count($results),
                0,
                GameResource::class . ' did not hard delete game'
            );

            return;
        }

        // Check soft deleted
        foreach ($results as $gameData) {
            $this->assertNotEmpty(
                $gameData['deleted'],
                GameResource::class . ' did not soft delete game'
            );

            return;
        }

        $this->fail(
            GameResource::class . ' hard deleted a game that should have been soft deleted'
        );
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $route
     * @param string $method
     * @param int $code
     * @param array $params
     * @param array $expectedDb
     * @param array $expectedResponse
     *
     * @dataProvider saveGameDataProvider
     */
    public function testItShouldSaveGame(
        string $login,
        string $route,
        int $code,
        string $method,
        array $params,
        array $expectedDb,
        array $expectedResponse
    ) {
        $this->dispatchAuthenticatedCall($login, $route, $code, $method, $params);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        if ($code > 400) {
            $this->assertEquals(
                $expectedResponse,
                $body,
                SaveGameResource::class . ' did not return correct errors'
            );

            return;
        }

        $this->assertNotEmpty(
            $body['created'],
            SaveGameResource::class . ' created is empty on game save'
        );

        $this->assertNotEmpty(
            $body['updated'],
            SaveGameResource::class . ' updated is empty on game save'
        );

        $this->assertNotEmpty(
            $body['game_id'],
            SaveGameResource::class . ' game_id is empty on game save'
        );

        $gameId = $body['game_id'];
        unset($body['updated'], $body['created'], $body['game_id'], $body['_links']['self']);

        $this->assertEquals(
            $expectedResponse,
            $body,
            SaveGameResource::class . ' did not return the expected response on creation'
        );

        // Check Database
        $stmt = $this->getConnection()->getConnection()
            ->query('SELECT * FROM games WHERE game_id = "' . $gameId . '"');

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        $dbData = null;
        foreach ($stmt as $dbData) {
            unset($dbData['created'], $dbData['updated'], $expectedResponse['_links']);
            $dbData['meta'] = Json::decode($dbData['meta'], Json::TYPE_ARRAY);
        }

        $expectedDb['game_id'] = $gameId;
        $this->assertEquals(
            $expectedDb,
            $dbData,
            SaveGameResource::class . ' data in Db does not match'
        );
    }

    /**
     * @return array
     * @codingStandardsIgnoreStart
     */
    public function fetchAllDataProvider()
    {
        return [
            'GET All' => [
                'super_user',
                '/game',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/game?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/game',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/game?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/game{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'game' => [
                            [
                                'game_id'     => 'no-flags',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'This game is not global',
                                'description' => 'This Game has no flags',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 1,
                                'coming_soon' => false,
                                'global'      => false,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/no-flags',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-unity',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Unity',
                                'description' => 'This game is global built in unity',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 2,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => true,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/global-unity',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-unity',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-desktop',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Desktop',
                                'description' => 'This game is global but desktop only',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 3,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => true,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/a3517fd6-60cb-11e6-a7d0-43afb27c9583',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-desktop',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global',
                                'description' => 'Just a global game',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 4,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-soon',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Coming soon',
                                'description' => 'This game is global and coming soon',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 5,
                                'coming_soon' => true,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-soon',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-featured',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Featured',
                                'description' => 'This Game is global and featured',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 6,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => true,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-featured',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 6,
                    'page'        => 1,
                ],
            ],

            'GET With soft deleted' => [
                'super_user',
                '/game?deleted=true',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/game?deleted=true&page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/game?deleted=true',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/game?deleted=true&page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/game?deleted=true{&per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'game' => [
                            [
                                'game_id'     => 'deleted-game',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'This game is deleted',
                                'description' => 'A Deleted Global Game',
                                'meta'        => [],
                                'deleted'     => '2016-04-13 00:00:00',
                                'sort_order'  => 1,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/deleted-game',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'no-flags',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'This game is not global',
                                'description' => 'This Game has no flags',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 1,
                                'coming_soon' => false,
                                'global'      => false,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/no-flags',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-unity',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Unity',
                                'description' => 'This game is global built in unity',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 2,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => true,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/global-unity',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-unity',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-desktop',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Desktop',
                                'description' => 'This game is global but desktop only',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 3,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => true,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/a3517fd6-60cb-11e6-a7d0-43afb27c9583',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-desktop',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global',
                                'description' => 'Just a global game',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 4,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-soon',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Coming soon',
                                'description' => 'This game is global and coming soon',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 5,
                                'coming_soon' => true,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-soon',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-featured',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Featured',
                                'description' => 'This Game is global and featured',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 6,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => true,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-featured',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 7,
                    'page'        => 1,
                ],
            ],

            'GET Featured and Coming soon' => [
                'super_user',
                '/game?featured=true&coming_soon=true',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/game?featured=true&coming_soon=true&page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/game?featured=true&coming_soon=true',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/game?featured=true&coming_soon=true&page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/game?featured=true&coming_soon=true{&per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'game' => [
                            [
                                'game_id'     => 'global-soon',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Coming soon',
                                'description' => 'This game is global and coming soon',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 5,
                                'coming_soon' => true,
                                'global'      => true,
                                'featured'    => false,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-soon',
                                    ],
                                ],
                            ],
                            [
                                'game_id'     => 'global-featured',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Featured',
                                'description' => 'This Game is global and featured',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 6,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => true,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-featured',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 2,
                    'page'        => 1,
                ],
            ],

            'GET Featured only' => [
                'super_user',
                '/game?featured=true',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/game?featured=true&page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/game?featured=true',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/game?featured=true&page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/game?featured=true{&per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'game' => [
                            [
                                'game_id'     => 'global-featured',
                                'created'     => '2016-04-13 00:00:00',
                                'updated'     => '2016-04-13 00:00:00',
                                'title'       => 'Global Featured',
                                'description' => 'This Game is global and featured',
                                'meta'        => [],
                                'deleted'     => null,
                                'sort_order'  => 6,
                                'coming_soon' => false,
                                'global'      => true,
                                'featured'    => true,
                                'unity'       => false,
                                'desktop'     => false,
                                '_links'      => [
                                    'thumb_url'  => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                                    ],
                                    'banner_url' => [
                                        'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                                    ],
                                    'game_url'   => [
                                        'href' => 'https://games.changemyworldnow.com/sea-turtle',
                                    ],
                                    'self'       => [
                                        'href' => 'http://api.test.com/game/global-featured',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 1,
                    'page'        => 1,
                ],
            ],

            'GET Game' => [
                'super_user',
                '/game/global-featured',
                200,
                [
                    'game_id'     => 'global-featured',
                    'created'     => '2016-04-13 00:00:00',
                    'updated'     => '2016-04-13 00:00:00',
                    'title'       => 'Global Featured',
                    'description' => 'This Game is global and featured',
                    'meta'        => [],
                    'deleted'     => null,
                    'sort_order'  => 6,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => true,
                    'unity'       => false,
                    'desktop'     => false,
                    '_links'      => [
                        'thumb_url'  => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        ],
                        'banner_url' => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        ],
                        'game_url'   => [
                            'href' => 'https://games.changemyworldnow.com/sea-turtle',
                        ],
                        'self'       => [
                            'href' => 'http://api.test.com/game/global-featured',
                        ],
                    ],
                ],
            ],

            'GET Deleted Game' => [
                'super_user',
                '/game/deleted-game',
                200,
                [
                    'game_id'     => 'deleted-game',
                    'created'     => '2016-04-13 00:00:00',
                    'updated'     => '2016-04-13 00:00:00',
                    'title'       => 'This game is deleted',
                    'description' => 'A Deleted Global Game',
                    'meta'        => [],
                    'deleted'     => '2016-04-13 00:00:00',
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    '_links'      => [
                        'thumb_url'  => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        ],
                        'banner_url' => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        ],
                        'game_url'   => [
                            'href' => 'https://games.changemyworldnow.com/sea-turtle',
                        ],
                        'self'       => [
                            'href' => 'http://api.test.com/game/deleted-game',
                        ],
                    ],
                ],
            ],

            'GET Game not found' => [
                'super_user',
                '/game/not-found',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Game not Found',
                ],
            ],

            'GET Adult access denied' => [
                'english_teacher',
                '/game',
                403,
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'GET Child access denied' => [
                'english_student',
                '/game',
                403,
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function saveGameDataProvider()
    {
        // @codingStandardsIgnoreStart
        return [
            'POST New Game' => [
                'super_user',
                '/game',
                201,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => [
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ],
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => '14',
                    'flags'       => '1',
                    'deleted'     => null,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'deleted'     => null,
                    '_links'      => [
                        'thumb_url'  => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        ],
                        'banner_url' => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        ],
                        'game_url'   => [
                            'href' => 'https://games.changemyworldnow.com/sea-turtle',
                        ],
                    ],
                ],
            ],

            'POST Only required fields and Json URLs' => [
                'super_user',
                '/game',
                201,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'sort_order'  => 14,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => '14',
                    'flags'       => '0',
                    'deleted'     => null,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => false,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'deleted'     => null,
                    '_links'      => [
                        'thumb_url'  => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        ],
                        'banner_url' => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        ],
                        'game_url'   => [
                            'href' => 'https://games.changemyworldnow.com/sea-turtle',
                        ],
                    ],
                ],

            ],

            'POST with missing fields' => [
                'super_user',
                '/game',
                422,
                'POST',
                [],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'title'       => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'description' => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'uris'        => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'sort_order'  => ['isEmpty' => 'Value is required and can\'t be empty'],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with incorrect sort order' => [
                'super_user',
                '/game',
                422,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 'apple',
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'sort_order' => [
                            'notDigits' => 'The input must contain only digits',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with Invalid Urls' => [
                'super_user',
                '/game',
                422,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'foo_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'baz_url' => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [thumb_url, banner_url, game_url]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with One Invalid Url' => [
                'super_user',
                '/game',
                422,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url'   => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'  => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [thumb_url, banner_url, game_url]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with non super adult' => [
                'english_teacher',
                '/game',
                403,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url'   => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'  => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'POST Fails with non super child' => [
                'english_student',
                '/game',
                403,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url'   => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'  => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'PUT Game' => [
                'super_user',
                '/game/no-flags',
                200,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => [
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ],
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => '14',
                    'flags'       => '1',
                    'deleted'     => null,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'deleted'     => null,
                    '_links'      => [
                        'thumb_url'  => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        ],
                        'banner_url' => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        ],
                        'game_url'   => [
                            'href' => 'https://games.changemyworldnow.com/sea-turtle',
                        ],
                    ],
                ],
            ],

            'PUT Only required fields and Json URLs' => [
                'super_user',
                '/game/no-flags',
                200,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'sort_order'  => 14,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => '14',
                    'flags'       => '0',
                    'deleted'     => null,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => false,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'deleted'     => null,
                    '_links'      => [
                        'thumb_url'  => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        ],
                        'banner_url' => [
                            'href' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        ],
                        'game_url'   => [
                            'href' => 'https://games.changemyworldnow.com/sea-turtle',
                        ],
                    ],
                ],

            ],

            'PUT with missing fields' => [
                'super_user',
                '/game/no-flags',
                422,
                'PUT',
                [],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'title'       => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'description' => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'uris'        => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'sort_order'  => ['isEmpty' => 'Value is required and can\'t be empty'],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with incorrect sort order' => [
                'super_user',
                '/game/no-flags',
                422,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 'apple',
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url'  => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'banner_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'   => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'sort_order' => [
                            'notDigits' => 'The input must contain only digits',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with Invalid Urls' => [
                'super_user',
                '/game/no-flags',
                422,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'foo_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'baz_url' => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [thumb_url, banner_url, game_url]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with One Invalid Url' => [
                'super_user',
                '/game/no-flags',
                422,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url'   => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'  => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [thumb_url, banner_url, game_url]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with non super adult' => [
                'english_teacher',
                '/game/no-flags',
                403,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url'   => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'  => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'PUT Fails with non super child' => [
                'english_student',
                '/game/no-flags',
                403,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'thumb_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url'   => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'game_url'  => 'https://games.changemyworldnow.com/sea-turtle',
                    ]),
                ],
                [],
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @return array
     */
    public function deleteGameProvider()
    {
        return [
            'Soft Delete Game' => [
                'super_user',
                'global',
                204,
                false,
            ],

            'Missing Game' => [
                'super_user',
                'foo-bar',
                404,
                false,
            ],

            // TODO Allow Resource To Hard delete game
            //            'Hard Delete Game' => [
            //                'super_user',
            //                'global',
            //                204,
            //                false,
            //            ],
        ];
    }
}
