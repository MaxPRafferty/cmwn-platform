<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\IntegrationTest as TestCase;
use Zend\Json\Json;

/**
 * Class GameDataResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class GameDataResourceTest extends TestCase
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
        return 'api\v1\rest\gamedata\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.game-data';
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $route
     * @param int $code
     * @param array $expectedResponse
     *
     * @dataProvider fetchGameProvider
     */
    public function testItShouldFetchGameData(
        string $login,
        string $route,
        int $code,
        array $expectedResponse
    ) {
        $this->dispatchAuthenticatedCall($login, $route, $code);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertEquals(
            $expectedResponse,
            $body,
            SaveGameResource::class . ' did not return correct response on fetch'
        );
    }

    /**
     * @return array
     */
    public function fetchGameProvider()
    {
        return [
            'Super User Fetch All' => [
                'super_user',
                '/game-data',
                200,
                [
                    '_embedded'   => [
                        'game-data' => [
                            [
                                'version' => '1.2.3',
                                'data'    => [
                                    'baz' => 'bat',
                                ],
                                'user_id' => 'english_student',
                                'game_id' => 'no-flags',
                                'created' => '2017-03-28 09:55:02',
                                '_links'  => [
                                    'self' => [
                                        'href' => 'http://api.test.com/game-data',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/game-data?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/game-data',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/game-data?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/game-data{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 1,
                    'page'        => 1,
                ],
            ],

            'Super User Fetch Game' => [
                'super_user',
                '/game-data/no-flags',
                200,
                [
                    '_embedded'   => [
                        'items' => [
                            [
                                'version' => '1.2.3',
                                'data'    => [
                                    'baz' => 'bat',
                                ],
                                'user_id' => 'english_student',
                                'game_id' => 'no-flags',
                                'created' => '2017-03-28 09:55:02',
                                '_links'  => [
                                    'self' => [
                                        'href' => 'http://api.test.com/game-data',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/game-data/no-flags?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/game-data/no-flags',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/game-data/no-flags?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/game-data/no-flags{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 30,
                    'total_items' => 1,
                    'page'        => 1,
                ],
            ],

            'Super User Invalid Game' => [
                'super_user',
                '/game-data/foo-bar',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Game not found',
                ],
            ],

            'Adult not authorized' => [
                'english_teacher',
                '/game-data/foo-bar',
                403,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Child not authorized' => [
                'english_student',
                '/game-data/foo-bar',
                403,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],
        ];
    }

}
