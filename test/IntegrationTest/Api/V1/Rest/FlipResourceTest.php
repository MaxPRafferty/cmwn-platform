<?php

namespace IntegrationTest\Api\V1\Rest;

use Api\V1\Rest\Flip\FlipResource;
use IntegrationTest\IntegrationTest as TestCase;
use Zend\Json\Json;

/**
 * Tests the flip resource
 */
class FlipResourceTest extends TestCase
{
    /**
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/flip.dataset.php');
    }

    /**
     * @inheritDoc
     */
    protected function getControllerNameForTest(): string
    {
        return 'api\v1\rest\flip\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.flip';
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $route
     * @param int $code
     * @param array $expected
     *
     * @dataProvider fetchFlipDataProvider
     */
    public function testItShouldFetchFlip(
        string $user,
        string $route,
        int $code,
        array $expected
    ) {
        $this->dispatchAuthenticatedCall($user, $route, $code);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        // Do not sort the arrays we want to make sure they match
        $this->assertEquals(
            $expected,
            $body,
            FlipResource::class . ' did not return the expected body'
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
     * @dataProvider saveFlipsDataProvider
     */
    public function testItShouldSaveFlips(
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
                FlipResource::class . ' did not return correct errors'
            );

            return;
        }

        $flipId = $body['flip_id'];
        unset($body['_links']['self']);

        $this->assertEquals(
            $expectedResponse,
            $body,
            FlipResource::class . ' did not return the expected response on creation'
        );

        // Check Database
        $stmt = $this->getConnection()->getConnection()
            ->query('SELECT * FROM flips WHERE flip_id = "' . $flipId . '"');

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        $dbData = null;
        foreach ($stmt as $dbData) {
            // nothing
        }

        $this->assertEquals(
            $expectedDb,
            $dbData,
            FlipResource::class . ' data in Db does not match'
        );
    }


    /**
     * @test
     *
     * @param string $login
     * @param string $flipId
     * @param int $code
     *
     * @dataProvider deleteFlipProvider
     */
    public function testItShouldDeleteFlip(string $login, string $flipId, int $code)
    {
        $this->dispatchAuthenticatedCall($login, '/flip/' . $flipId, $code, 'DELETE');

        if ($code > 400) {
            return;
        }

        $this->assertEmpty(
            $this->getResponse()->getContent(),
            FlipResource::class . ' did not return empty body when deleting a game'
        );

        $results = $this->getConnection()
            ->getConnection()
            ->query('SELECT * FROM flips WHERE flip_id = "' . $flipId . '" LIMIT 1');

        foreach ($results as $row) {
            $this->fail(
                FlipResource::class . ' did not delete flip'
            );
        }

    }

    /**
     * @return array
     * @codingStandardsIgnoreStart
     */
    public function fetchFlipDataProvider()
    {
        return [
            'GET All For super' => [
                'super_user',
                '/flip',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/flip?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/flip',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/flip?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/flip{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'flips' => [
                            [
                                'flip_id'     => 'earned-flip-multiple-times',
                                'title'       => 'Earned flip multiple times',
                                'description' => 'This is a flip that is earned by a user multiple times',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-multiple-times',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'earned-flip-multiple-users',
                                'title'       => 'Earned flip multiple users',
                                'description' => 'By Multiple users',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-multiple-users',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'earned-flip-once',
                                'title'       => 'Earned Flip Once',
                                'description' => 'This is a flip that is earned by a user once',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-once',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'flip',
                                'title'       => 'Flip',
                                'description' => 'This is just a flip',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/flip',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 4,
                    'page'        => 1,
                ],
            ],

            'GET All For adult (English Teacher)' => [
                'english_teacher',
                '/flip',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/flip?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/flip',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/flip?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/flip{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'flips' => [
                            [
                                'flip_id'     => 'earned-flip-multiple-times',
                                'title'       => 'Earned flip multiple times',
                                'description' => 'This is a flip that is earned by a user multiple times',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-multiple-times',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'earned-flip-multiple-users',
                                'title'       => 'Earned flip multiple users',
                                'description' => 'By Multiple users',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-multiple-users',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'earned-flip-once',
                                'title'       => 'Earned Flip Once',
                                'description' => 'This is a flip that is earned by a user once',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-once',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'flip',
                                'title'       => 'Flip',
                                'description' => 'This is just a flip',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/flip',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 4,
                    'page'        => 1,
                ],
            ],

            'GET All For child (English Student)' => [
                'english_student',
                '/flip',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/flip?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/flip',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/flip?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/flip{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'flips' => [
                            [
                                'flip_id'     => 'earned-flip-multiple-times',
                                'title'       => 'Earned flip multiple times',
                                'description' => 'This is a flip that is earned by a user multiple times',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-multiple-times',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'earned-flip-multiple-users',
                                'title'       => 'Earned flip multiple users',
                                'description' => 'By Multiple users',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-multiple-users',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'earned-flip-once',
                                'title'       => 'Earned Flip Once',
                                'description' => 'This is a flip that is earned by a user once',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/earned-flip-once',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                            [
                                'flip_id'     => 'flip',
                                'title'       => 'Flip',
                                'description' => 'This is just a flip',
                                '_links'      => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/flip/flip',
                                    ],
                                    'earned'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                                    ],
                                    'unearned' => [
                                        'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                                    ],
                                    'static'   => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'coin'     => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                    'default'  => [
                                        'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'page_count'  => 1,
                    'page_size'   => 100,
                    'total_items' => 4,
                    'page'        => 1,
                ],
            ],

            'GET Flip For super' => [
                'super_user',
                '/flip/flip',
                200,
                [
                    'flip_id'     => 'flip',
                    'title'       => 'Flip',
                    'description' => 'This is just a flip',
                    '_links'      => [
                        'self'     => [
                            'href' => 'http://api.test.com/flip/flip',
                        ],
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'GET Flip 404 For super' => [
                'super_user',
                '/flip/not-found',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Flip not Found',
                ],
            ],

            'GET Flip For Adult (English Teacher)' => [
                'english_teacher',
                '/flip/flip',
                200,
                [
                    'flip_id'     => 'flip',
                    'title'       => 'Flip',
                    'description' => 'This is just a flip',
                    '_links'      => [
                        'self'     => [
                            'href' => 'http://api.test.com/flip/flip',
                        ],
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'GET Flip 404 For Adult (English Teacher)' => [
                'english_teacher',
                '/flip/not-found',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Flip not Found',
                ],
            ],

            'GET Flip For Adult (English Student)' => [
                'english_student',
                '/flip/flip',
                200,
                [
                    'flip_id'     => 'flip',
                    'title'       => 'Flip',
                    'description' => 'This is just a flip',
                    '_links'      => [
                        'self'     => [
                            'href' => 'http://api.test.com/flip/flip',
                        ],
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'GET Flip 404 For Adult (English Student)' => [
                'english_student',
                '/flip/not-found',
                404,
                [
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'title'  => 'Not Found',
                    'status' => 404,
                    'detail' => 'Flip not Found',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function saveFlipsDataProvider()
    {
        return [
            'POST New Flip' => [
                'super_user',
                '/flip',
                201,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [
                    'flip_id'     => 'manchuck-farmville',
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => Json::encode([
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'flip_id'     => 'manchuck-farmville',
                    '_links'      => [
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'POST With Json URLs' => [
                'super_user',
                '/flip',
                201,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => Json::encode([
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [
                    'flip_id'     => 'manchuck-farmville',
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => Json::encode([
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'flip_id'     => 'manchuck-farmville',
                    '_links'      => [
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'POST with missing fields' => [
                'super_user',
                '/flip',
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
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with Invalid Urls' => [
                'super_user',
                '/flip',
                422,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'foo_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'baz_url' => 'https://flips.changemyworldnow.com/sea-turtle',
                    ],
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [earned, unearned, static, coin, default]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with One Invalid Url' => [
                'super_user',
                '/flip',
                422,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'foo_url'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [earned, unearned, static, coin, default]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with non http Urls' => [
                'super_user',
                '/flip',
                422,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'meta'        => [],
                    'sort_order'  => 1,
                    'coming_soon' => false,
                    'global'      => true,
                    'featured'    => false,
                    'unity'       => false,
                    'desktop'     => false,
                    'uris'        => Json::encode([
                        'earned'   => 'http://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'http://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'http://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'http://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'http://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'invalidScheme' => 'Uri must be from a secure domain',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'POST Fails with no access for adult' => [
                'english_teacher',
                '/flip',
                403,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [],
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'POST Fails with no access for child' => [
                'english_student',
                '/flip',
                403,
                'POST',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [],
                [
                    'title'  => 'Forbidden',
                    'type'   => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status' => 403,
                    'detail' => 'Not Authorized',
                ],
            ],

            'PUT Flip wont change Id' => [
                'super_user',
                '/flip/flip',
                200,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [
                    'flip_id'     => 'flip',
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => Json::encode([
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'flip_id'     => 'flip',
                    '_links'      => [
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'PUT with Json URLs' => [
                'super_user',
                '/flip/flip',
                200,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => Json::encode([
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [
                    'flip_id'     => 'flip',
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => Json::encode([
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ]),
                ],
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'flip_id'     => 'flip',
                    '_links'      => [
                        'earned'   => [
                            'href' => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        ],
                        'unearned' => [
                            'href' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        ],
                        'static'   => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'coin'     => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                        'default'  => [
                            'href' => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        ],
                    ],
                ],
            ],

            'PUT with missing fields' => [
                'super_user',
                '/flip/flip',
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
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with Invalid Urls' => [
                'super_user',
                '/flip/flip',
                422,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'foo_url' => 'https://s-media-cache-ak0.pinimg.com/736x/62/01/de/6201de2e20a31bfd4b44267337e3486e.jpg',
                        'bar_url' => 'https://s-media-cache-ak0.pinimg.com/originals/82/d7/2a/82d72a9e5e75c73d1a68d562b3d86da6.jpg',
                        'baz_url' => 'https://flips.changemyworldnow.com/sea-turtle',
                    ],
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [earned, unearned, static, coin, default]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with One Invalid Url' => [
                'super_user',
                '/flip/flip',
                422,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'https://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'https://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'foo_url'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'https://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'missingKey' =>
                                'Missing keys or invalid key set expected: [earned, unearned, static, coin, default]',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with non ssl uris' => [
                'super_user',
                '/flip/flip',
                422,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
                    'uris'        => [
                        'earned'   => 'http://media.changemyworldnow.com/f/65e520c9fca8531d935967f1ddba7c4b.gif',
                        'unearned' => 'http://media.changemyworldnow.com/f/44622644f9eb3bfce8640649c2dbe3b4.gif',
                        'static'   => 'http://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'coin'     => 'http://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                        'default'  => 'http://media.changemyworldnow.com/f/9fda7f0ebf5605365e3ad4baab0d45bc.gif',
                    ],
                ],
                [],
                [
                    'title'               => 'Unprocessable Entity',
                    'validation_messages' => [
                        'uris' => [
                            'invalidScheme' => 'Uri must be from a secure domain',
                        ],
                    ],
                    'type'                => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                    'status'              => 422,
                    'detail'              => 'Failed Validation',
                ],
            ],

            'PUT Fails with non super adult' => [
                'english_teacher',
                '/flip/no-flags',
                403,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
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
                        'flip_url'  => 'https://flips.changemyworldnow.com/sea-turtle',
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
                '/flip/no-flags',
                403,
                'PUT',
                [
                    'title'       => 'Manchuck Farmville',
                    'description' => 'The best flip ever (Just like franks hot sauce)',
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
                        'flip_url'  => 'https://flips.changemyworldnow.com/sea-turtle',
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
    public function deleteFlipProvider()
    {
        return [
            'DELETE Flip' => [
                'super_user',
                'flip',
                204,
                false,
            ],

            'DELETE Flip Fails for Adult' => [
                'english_teacher',
                'flip',
                403,
                false,
            ],

            'DELETE Flip Fails for Child' => [
                'english_student',
                'flip',
                403,
                false,
            ],

            'DELETE 404 Missing flip' => [
                'super_user',
                'foo-bar',
                404,
                false,
            ],
        ];
    }
}
