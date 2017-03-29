<?php

namespace IntegrationTest\Api\V1\Rest;

use Api\V1\Rest\UserGame\UserGameResource;
use IntegrationTest\IntegrationTest;
use Zend\Json\Json;

/**
 * Integration tests for UserGameResource
 */
class UserGameResourceTest extends IntegrationTest
{
    /**
     * @inheritdoc
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/games.dataset.php');
    }

    /**
     * @inheritDoc
     */
    protected function getControllerNameForTest(): string
    {
        return 'api\v1\rest\usergame\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.user-game';
    }

    /**
     * @test
     *
     * @param string $userId
     * @param string $route
     * @param int $code
     * @param array $expected
     *
     * @dataProvider fetchAllDataProvider
     */
    public function testItShouldFetchGame(string $userId, string $route, int $code, array $expected)
    {
        $this->dispatchAuthenticatedCall($userId, $route, $code);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        // Do not sort the arrays we want to make sure they match
        $this->assertEquals(
            $expected,
            $body,
            UserGameResource::class . ' did not return the expected body for ' . $route
        );
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $userId
     * @param string $gameId
     * @param string $route
     * @param int $code
     * @param array $expected
     *
     * @dataProvider attachGamesDataProvider
     */
    public function testItShouldAttachGamesToUsers(
        string $login,
        string $userId,
        string $gameId,
        string $route,
        int $code,
        array $expected
    ) {
        $this->dispatchAuthenticatedCall($login, $route, $code, 'POST');
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        // Do not sort the arrays we want to make sure they match
        $this->assertEquals(
            $expected,
            $body,
            UserGameResource::class . ' did not return the expected body'
        );

        if ($code > 400) {
            return;
        }

        $stmt = $this->getConnection()->getConnection()->query(
            'SELECT * FROM user_games WHERE user_id = "' . $userId . '" AND game_id = "' . $gameId . '"'
        );

        $dbData = null;
        foreach ($stmt as $dbData) {
            // nothing to check we just want to know the record exists
        }

        $this->assertNotNull(
            $dbData,
            UserGameResource::class . 'did not attach the game to the user'
        );
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $userId
     * @param string $gameId
     * @param string $route
     * @param int $code
     * @param array $expected
     *
     * @dataProvider detachGamesDataProvider
     */
    public function testItShouldDetachGamesToUsers(
        string $login,
        string $userId,
        string $gameId,
        string $route,
        int $code,
        array $expected
    ) {
        $this->dispatchAuthenticatedCall($login, $route, $code, 'DELETE');

        if ($code > 400) {
            $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

            // Do not sort the arrays we want to make sure they match
            $this->assertEquals(
                $expected,
                $body,
                UserGameResource::class . ' did not return the expected body'
            );

            return;
        }

        $stmt = $this->getConnection()->getConnection()->query(
            'SELECT * FROM user_games WHERE user_id = "' . $userId . '" AND game_id = "' . $gameId . '"'
        );

        $dbData = null;
        foreach ($stmt as $dbData) {
            // nothing to check we just want to know the record exists
        }

        $this->assertNull(
            $dbData,
            UserGameResource::class . 'did not detach the game to the user'
        );
    }

    /**
     * @return array
     * @codingStandardsIgnoreStart
     */
    public function fetchAllDataProvider()
    {
        return [
            'GET All for adult' => [
                'english_teacher',
                '/user/english_teacher/game',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_teacher/game?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_teacher/game',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_teacher/game?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_teacher/game{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'game' => [
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global-unity',
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global-desktop',
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global',
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global-soon',
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global-featured',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 5,
                    'page'        => 1,
                ],
            ],

            'GET Featured and Coming soon for adult' => [
                'english_teacher',
                '/user/english_teacher/game?featured=true&coming_soon=true',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_teacher/game?featured=true&coming_soon=true&page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_teacher/game?featured=true&coming_soon=true',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_teacher/game?featured=true&coming_soon=true&page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_teacher/game?featured=true&coming_soon=true{&per_page,page}',
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global-soon',
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
                                        'href' => 'http://api.test.com/user/english_teacher/game/global-featured',
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

            'GET Featured and Coming soon for child' => [
                'english_student',
                '/user/english_student/game?featured=true&coming_soon=true',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_student/game?featured=true&coming_soon=true&page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_student/game?featured=true&coming_soon=true',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_student/game?featured=true&coming_soon=true&page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_student/game?featured=true&coming_soon=true{&per_page,page}',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global-soon',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global-featured',
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

            'GET All for user that has no attached games' => [
                'other_student',
                '/user/other_student/game',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/other_student/game?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/other_student/game',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/other_student/game?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/other_student/game{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'game' => [
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
                                        'href' => 'http://api.test.com/user/other_student/game/global-unity',
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
                                        'href' => 'http://api.test.com/user/other_student/game/global-desktop',
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
                                        'href' => 'http://api.test.com/user/other_student/game/global',
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
                                        'href' => 'http://api.test.com/user/other_student/game/global-soon',
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
                                        'href' => 'http://api.test.com/user/other_student/game/global-featured',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 5,
                    'page'        => 1,
                ],
            ],

            'GET All for user that has an attached game' => [
                'english_student',
                '/user/english_student/game',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_student/game?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_student/game',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_student/game?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_student/game{?per_page,page}',
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
                                        'href' => 'http://api.test.com/user/english_student/game/no-flags',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global-unity',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global-desktop',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global-soon',
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
                                        'href' => 'http://api.test.com/user/english_student/game/global-featured',
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

            'GET Game for Adult' => [
                'english_teacher',
                '/user/english_teacher/game/global-featured',
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
                            'href' => 'http://api.test.com/user/english_teacher/game/global-featured',
                        ],
                    ],
                ],
            ],

            'GET Game for Child' => [
                'english_student',
                '/user/english_student/game/global-featured',
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
                            'href' => 'http://api.test.com/user/english_student/game/global-featured',
                        ],
                    ],
                ],
            ],

            'GET Game for User with access ' => [
                'english_student',
                '/user/english_student/game/no-flags',
                200,
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
                            'href' => 'http://api.test.com/user/english_student/game/no-flags',
                        ],
                    ],
                ],
            ],

            'GET Game for User 404 with no access' => [
                'other_student',
                '/user/other_student/game/no-flags',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Game not found',
                ],
            ],

            'GET Game for User 404 for invalid game' => [
                'other_student',
                '/user/other_student/game/not-real',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Game not Found',
                ],
            ],

            'GET Game for User 404 for invalid user' => [
                'other_student',
                '/user/not-a-user/game/not-real',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'User not found',
                ],
            ],

            'GET Deleted Game 404 for Adult' => [
                'english_teacher',
                '/user/english_teacher/game/deleted-game',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Entity not found',
                ],
            ],

            'GET Deleted Game 404 for child' => [
                'english_student',
                '/user/english_student/game/deleted-game',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Entity not found',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function attachGamesDataProvider()
    {
        return [
            'Attach game to adult' => [
                'super_user',
                'english_teacher',
                'no-flags',
                '/user/english_teacher/game/no-flags',
                201,
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
                            'href' => 'http://api.test.com/user/english_teacher/game/no-flags',
                        ],
                    ],
                ],
            ],

            'Attach game to child' => [
                'super_user',
                'other_student',
                'no-flags',
                '/user/other_student/game/no-flags',
                201,
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
                            'href' => 'http://api.test.com/user/other_student/game/no-flags',
                        ],
                    ],
                ],
            ],

            'Fails when game not found' => [
                'super_user',
                'other_student',
                'not-real',
                '/user/other_student/game/not-real',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Game not Found',
                ],
            ],

            'Fails when adult not allowed' => [
                'english_teacher',
                'other_student',
                'no-flags',
                '/user/other_student/game/no-flags',
                403,
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Fails when child not allowed' => [
                'english_student',
                'other_student',
                'no-flags',
                '/user/other_student/game/no-flags',
                403,
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Fails when user not found' => [
                'super_user',
                'no-found',
                'no-flags',
                '/user/not-found/game/no-flags',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'User not found',
                ],
            ],

            // TODO add check for adding the same game once CORE-3324 merged
        ];
    }

    /**
     * @return array
     */
    public function detachGamesDataProvider()
    {
        return [
            'Detach game' => [
                'super_user',
                'english_student',
                'no-flags',
                '/user/english_student/game/no-flags',
                204,
                [],
            ],

            'Fails when game not found' => [
                'super_user',
                'other_student',
                'not-real',
                '/user/other_student/game/not-real',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'Game not Found',
                ],
            ],

            'Fails when adult not allowed' => [
                'english_teacher',
                'other_student',
                'no-flags',
                '/user/other_student/game/no-flags',
                403,
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Fails when child not allowed' => [
                'english_student',
                'other_student',
                'no-flags',
                '/user/other_student/game/no-flags',
                403,
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Fails when user not found' => [
                'super_user',
                'no-found',
                'no-flags',
                '/user/not-found/game/no-flags',
                404,
                [
                    'title'  => 'Not Found',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 404,
                    'detail' => 'User not found',
                ],
            ],
        ];
    }
}
