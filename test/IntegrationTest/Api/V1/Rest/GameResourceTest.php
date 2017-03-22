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
     * @param string $user    - User to login
     * @param string $route   - Route with query params
     * @param array $expected - list of expected game ids
     *
     * @dataProvider fetchAllDataProvider
     */
    public function testItShouldFetchAllGames($user, $route, array $expected)
    {
        $this->dispatchAuthenticatedCall($user, $route);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey(
            '_embedded',
            $body,
            GameResource::class . ' did not return _embedded in body for ' . $route
        );

        $this->assertArrayHasKey(
            'game',
            $body['_embedded'],
            GameResource::class . ' did not return _embedded in _embedded for ' . $route
        );

        $games = $body['_embedded']['game'];

        $actual = [];
        foreach ($games as $game) {
            $actual[] = $game['game_id'];
        }

        // Do not sort the arrays we want to make sure they match
        $this->assertEquals(
            $expected,
            $actual,
            GameResource::class . ' did not return the correct number of games for ' . $route
        );
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $gameId
     * @param array $expectedData
     *
     * @dataProvider fetchGameProvider
     */
    public function testItShouldFetchGame(string $login, string $gameId, int $code, array $expectedData)
    {
        $this->dispatchAuthenticatedCall($login, '/game/' . $gameId, $code);
        $this->assertEquals(
            $expectedData,
            Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY),
            GameResource::class . ' did not return the expected response when fetching a game by id'
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
     * @param int $code
     * @param array $gameData
     * @param array $expectedResponse
     *
     * @dataProvider createGameProvider
     */
    public function testItShouldCreateGame(
        string $login,
        int $code,
        array $gameData,
        array $expectedResponse
    ) {
        $this->dispatchAuthenticatedCall($login, '/game', $code, 'POST', $gameData);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        if ($code === 422) {
            $this->assertEquals(
                $expectedResponse,
                $body,
                GameResource::class . ' did not return correct validation errors'
            );

            return;
        }

        $this->assertNotEmpty(
            $body['game_id'],
            GameResource::class . ' game id is empty on game creation'
        );

        $this->assertNotEmpty(
            $body['created'],
            GameResource::class . ' created is empty on game creation'
        );

        $this->assertNotEmpty(
            $body['updated'],
            GameResource::class . ' updated is empty on game creation'
        );

        // Check the game was written to the DB
        $this->assertTableRowCount(
            'games',
            8,
            GameResource::class . ' game was not saved to the DB'
        );

        // These fields cannot be matched as they are generated at runtime
        unset($body['game_id']);
        unset($body['created']);
        unset($body['updated']);
        unset($body['deleted']);
        unset($body['_links']['self']);

        $this->assertEquals(
            $expectedResponse,
            $body,
            GameResource::class . ' did not return the expected response on creation'
        );
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $gameId
     * @param int $code
     * @param array $params
     * @param array $expectedResponse
     *
     * @dataProvider updateGameProvider
     */
    public function testItShouldUpdateGame(
        string $login,
        string $gameId,
        int $code,
        array $params,
        array $expectedResponse
    ) {
        $this->dispatchAuthenticatedCall($login, '/game/' . $gameId, $code, 'PUT', $params);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        if ($code === 422) {
            $this->assertEquals(
                $expectedResponse,
                $body,
                GameResource::class . ' did not return correct validation errors'
            );

            return;
        }

        // Check the game was written to the DB
        $gameQuery = $this->getConnection()->getConnection()
            ->query('SELECT * FROM games WHERE game_id = "' . $gameId . '"', \PDO::FETCH_ASSOC);

        foreach ($gameQuery as $gameResult) {
            $this->assertNull(
                $gameResult['deleted'],
                GameResource::class . ' DB record shows game as deleted'
            );

            unset($gameResult['updated']);
            unset($gameResult['created']);
            unset($gameResult['deleted']);
            $gameResult['meta'] = Json::decode($gameResult['meta'], Json::TYPE_ARRAY);
            $gameResult['uris'] = Json::decode($gameResult['uris'], Json::TYPE_ARRAY);
        }

        $params['game_id'] = $gameId;
        foreach ($gameResult as $key => $value) {
            $this->assertEquals(
                $params[$key] ?? null,
                $value,
                GameResource::class . ' DB field mis-matches field: ' . $key
            );
        }

        // These fields cannot be matched as they are generated at runtime
        unset($body['game_id']);
        unset($body['created']);
        unset($body['deleted']);
        unset($body['updated']);
        unset($body['_links']['self']);

        $this->assertEquals(
            $expectedResponse,
            $body,
            GameResource::class . ' did not return the expected response on creation'
        );
    }

    /**
     * @return array
     */
    public function fetchAllDataProvider()
    {
        return [
            'Default' => [
                'super_user',
                '/game',
                [
                    'no-flags',
                    'global-unity',
                    'global-desktop',
                    'global',
                    'global-soon',
                    'global-featured',
                ],
            ],

            'With Soft Deleted' => [
                'super_user',
                '/game?deleted=true',
                [
                    'deleted-game',
                    'no-flags',
                    'global-unity',
                    'global-desktop',
                    'global',
                    'global-soon',
                    'global-featured',
                ],
            ],

            'With Featured' => [
                'super_user',
                '/game?featured=true',
                [
                    'global-featured',
                ],
            ],

            'With Featured and Desktop' => [
                'super_user',
                '/game?featured=true&desktop=true',
                [
                    'global-desktop',
                    'global-featured',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function fetchGameProvider()
    {
        // @codingStandardsIgnoreStart
        return [
            'Fetch Global Game' => [
                'super_user',
                'global',
                200,
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
                        'self'       => [
                            'href' => 'http://api.test.com/game/global',
                        ],
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

            'Fetch Deleted Game' => [
                'super_user',
                'deleted-game',
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
                        'self'       => [
                            'href' => 'http://api.test.com/game/deleted-game',
                        ],
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

            'Fetch 404 GameGame' => [
                'super_user',
                'not-found',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Game not found',
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

    /**
     * @return array
     */
    public function createGameProvider()
    {
        // @codingStandardsIgnoreStart
        return [
            'Create Game' => [
                'super_user',
                201,
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
                    'sort_order'  => 14,
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
                    ],
                ],
            ],

            'Create Game With Json URIS' => [
                'super_user',
                201,
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
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * @return array
     */
    public function updateGameProvider()
    {
        // @codingStandardsIgnoreStart
        return [
            'Update Game' => [
                'super_user',
                'global-featured',
                200,
                [
                    'game_id'     => 'foo-bar', // checking to make sure the id does not change
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'flags'       => 3,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => true,
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
                    'sort_order'  => 14,
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
                    ],
                ],
            ],

            'Deleted Game Now un deleted' => [
                'super_user',
                'deleted-game',
                200,
                [
                    'game_id'     => 'foo-bar', // checking to make sure the id does not change
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best game ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 14,
                    'flags'       => 3,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => true,
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
                    'sort_order'  => 14,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => true,
                    'unity'       => false,
                    'desktop'     => false,
                    '_links' => [
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
        ];
        // @codingStandardsIgnoreEnd
    }
}
