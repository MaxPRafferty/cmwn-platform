<?php

namespace IntegrationTest\Api\V1\Rest;

use Api\V1\Rest\SaveGame\SaveGameResource;
use Game\Service\SaveGameServiceInterface;
use IntegrationTest\IntegrationTest as TestCase;
use IntegrationTest\TestHelper;
use Zend\Json\Exception\RuntimeException;
use Zend\Json\Json;

/**
 * Test SaveGameResourceTest
 */
class SaveGameResourceTest extends TestCase
{
    /**
     * @var SaveGameServiceInterface
     */
    protected $saveService;

    /**
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/games.dataset.php');
    }

    /**
     * @before
     */
    public function setUpSaveService()
    {
        $this->saveService = TestHelper::getServiceManager()->get(SaveGameServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    protected function getControllerNameForTest(): string
    {
        return 'api\v1\rest\savegame\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.save-game';
    }

    /**
     * @test
     *
     * @param string $login
     * @param string $userId
     * @param string $gameId
     * @param int $code
     * @param array $data
     * @param array $expectedResponse
     *
     * @dataProvider saveGameDataProvider
     */
    public function testItShouldSaveGameData(
        string $login,
        string $userId,
        string $gameId,
        int $code,
        array $data,
        array $expectedResponse
    ) {
        $this->dispatchAuthenticatedCall($login, '/user/' . $userId . '/save/' . $gameId, $code, 'PUT', $data);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        if ($code !== 200) {
            $this->assertEquals(
                $expectedResponse,
                $body,
                SaveGameResource::class . ' did not return correct errors'
            );

            return;
        }

        // Check response body
        $this->assertNotEmpty(
            $body['created'],
            SaveGameResource::class . ' updated is empty on game creation'
        );

        unset($body['created']);

        $this->assertEquals(
            $expectedResponse,
            $body,
            SaveGameResource::class . ' did not return the expected response on creation'
        );

        // Check Database
        $stmt = $this->getConnection()->getConnection()
            ->query('SELECT * FROM user_saves WHERE user_id = "' . $userId . '" AND game_id = "' . $gameId . '"');

        $stmt->setFetchMode(\PDo::FETCH_ASSOC);

        foreach ($stmt as $dbData) {
            unset($dbData['created'], $expectedResponse['_links']);
            $dbData['data'] = Json::decode($dbData['data'], Json::TYPE_ARRAY);
        }

        $this->assertEquals(
            $expectedResponse,
            $dbData,
            SaveGameResource::class . ' data in Db does not match'
        );
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
     * @test
     *
     * @param string $login
     * @param string $route
     * @param int $code
     * @param array $expectedResponse
     *
     * @dataProvider deleteGameProvider
     */
    public function testItShouldDeleteSaveData(
        string $login,
        string $route,
        int $code,
        $expectedResponse
    ) {
        $this->assertTableRowCount(
            'user_saves',
            1,
            SaveGameResource::class . ' db is starting with expected records'
        );

        $this->dispatchAuthenticatedCall($login, $route, $code, 'DELETE');
        $body = $this->getResponse()->getContent();
        try {
            $body = Json::decode($body, Json::TYPE_ARRAY);
        } catch (RuntimeException $decode) {
            // empty body
        }

        $this->assertEquals(
            $expectedResponse,
            $body,
            SaveGameResource::class . ' did not return correct response on deleting'
        );

        if ($code != 204) {
            $this->assertTableRowCount(
                'user_saves',
                1,
                SaveGameResource::class . ' returned invalid code but still deleted save data'
            );

            return;
        }

        $this->assertTableRowCount(
            'user_saves',
            0,
            SaveGameResource::class . ' did not delete the save game'
        );
    }

    /**
     * @return array
     */
    public function deleteGameProvider()
    {
        return [
            'English Student deleting game' => [
                'english_student',
                '/user/english_student/save/no-flags',
                204,
                '',
            ],

            'English Student game not found' => [
                'english_student',
                '/user/english_student/save/foo-bar',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Game not found',
                ],
            ],

            'User not found' => [
                'super_user',
                '/user/foo-bar/save/no-flags',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'User not found',
                ],
            ],

            'Other Student deleting' => [
                'other_student',
                '/user/english_student/save/no-flags',
                403,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Principal deleting' => [
                'principal',
                '/user/english_student/save/no-flags',
                403,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Super deleting' => [
                'super_user',
                '/user/english_student/save/no-flags',
                204,
                '',
            ],
        ];
    }

    /**
     * @return array
     */
    public function fetchGameProvider()
    {
        return [
            'English Student' => [
                'english_student',
                '/user/english_student/save',
                200,
                [
                    '_embedded'   => [
                        'save_game' => [
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
                                        'href' => 'http://api.test.com/user/english_student/save/no-flags',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_student/save?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_student/save',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_student/save?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_student/save{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 1,
                    'page'        => 1,
                ],
            ],

            'English Student to game' => [
                'english_student',
                '/user/english_student/save/no-flags',
                200,
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
                            'href' => 'http://api.test.com/user/english_student/save/no-flags',
                        ],
                    ],
                ],
            ],

            'English teacher' => [
                'english_teacher',
                '/user/english_teacher/save/no-flags',
                403,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'English Student with no save' => [
                'english_student',
                '/user/english_student/save/global-featured',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'No Save game Found',
                ],
            ],

            'English Student with invalid game' => [
                'english_student',
                '/user/english_student/save/foo-bar',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Game not found',
                ],
            ],

            'User not found' => [
                'english_student',
                '/user/foo-bar/save/global-featured',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'User not found',
                ],
            ],

            'Other Student to English' => [
                'other_student',
                '/user/english_student/save/no-flags',
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

    /**
     * @return array
     */
    public function saveGameDataProvider()
    {
        return [
            'English Student Saving to them self' => [
                'english_student',
                'english_student',
                'global-featured',
                200,
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                ],
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                    'user_id' => 'english_student',
                    'game_id' => 'global-featured',
                    '_links'  => [
                        'self' => [
                            'href' => 'http://api.test.com/user/english_student/save/global-featured',
                        ],
                    ],
                ],
            ],

            'English Student Saving updated' => [
                'english_student',
                'english_student',
                'no-flags',
                200,
                [
                    'user_id' => 'foo-bar',
                    'game_id' => 'fizz-buzz',
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                ],
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                    'user_id' => 'english_student',
                    'game_id' => 'no-flags',
                    '_links'  => [
                        'self' => [
                            'href' => 'http://api.test.com/user/english_student/save/no-flags',
                        ],
                    ],
                ],
            ],

            'English Teacher Saving to them self' => [
                'english_teacher',
                'english_teacher',
                'global-featured',
                403,
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                ],
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Principal Saving to them self' => [
                'principal',
                'principal',
                'global-featured',
                403,
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                ],
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'English Student with Json' => [
                'english_student',
                'english_student',
                'global-featured',
                200,
                [
                    'version' => '1.0.1',
                    'data'    => Json::encode([
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ]),
                ],
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                    'user_id' => 'english_student',
                    'game_id' => 'global-featured',
                    '_links'  => [
                        'self' => [
                            'href' => 'http://api.test.com/user/english_student/save/global-featured',
                        ],
                    ],
                ],
            ],

            'English Student Saving to other' => [
                'english_student',
                'other_student',
                'global-featured',
                403,
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                ],
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Forbidden',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'Other student not allowed to access game' => [
                'other_student',
                'other_student',
                'no-flags',
                404,
                [
                    'version' => '1.0.1',
                    'data'    => [
                        'foo'  => 'bar',
                        'fizz' => 'buzz',
                    ],
                ],
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Game not found',
                ],
            ],

            'English Student with missing fields' => [
                'english_student',
                'english_student',
                'global-featured',
                422,
                [],
                [
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'               => 'Unprocessable Entity',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                    'validation_messages' => [
                        'data'    => ['isEmpty' => 'Value is required and can\'t be empty'],
                        'version' => ['isEmpty' => 'Value is required and can\'t be empty'],
                    ],
                ],
            ],
        ];
    }
}
