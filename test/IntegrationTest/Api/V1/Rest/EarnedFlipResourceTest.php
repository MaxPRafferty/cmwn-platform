<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\IntegrationTest as TestCase;
use Zend\Json\Json;

/**
 * Tests the earned Flip Resource
 */
class EarnedFlipResourceTest extends TestCase
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
        return 'api\v1\rest\earnedflip\controller';
    }

    /**
     * @inheritDoc
     */
    protected function getControllerRouteNameForTest(): string
    {
        return 'api.rest.flip-user';
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $route
     * @param int $code
     * @param array $expected
     *
     * @dataProvider fetchEarnedFlipDataProvider
     */
    public function testItShouldFetchAllEarnedFlips(
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
     * @return array
     * @codingStandardsIgnoreStart
     */
    public function fetchEarnedFlipDataProvider()
    {
        return [
            'GET All For super to self' => [
                'super_user',
                '/user/super_user/flip',
                200,
                [
                    '_links'      => [
                        'self' => [
                            'href' => 'http://api.test.com/user/super_user/flip',
                        ],
                    ],
                    '_embedded'   => [
                        'earned_flip' => [],
                    ],
                    'page_count'  => 0,
                    'page_size'   => 100,
                    'total_items' => 0,
                    'page'        => 0,
                ],
            ],

            'GET All For adult with no flips to self' => [
                'english_teacher',
                '/user/english_teacher/flip',
                200,
                [
                    '_links'      => [
                        'self' => [
                            'href' => 'http://api.test.com/user/english_teacher/flip',
                        ],
                    ],
                    '_embedded'   => [
                        'earned_flip' => [],
                    ],
                    'page_count'  => 0,
                    'page_size'   => 100,
                    'total_items' => 0,
                    'page'        => 0,
                ],
            ],

            'GET All For child with flips to self' => [
                'english_student',
                '/user/english_student/flip',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_student/flip?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_student/flip',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_student/flip?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_student/flip{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'earned_flip' => [
                            [
                                'flip_id'         => 'earned-flip-multiple-users',
                                'title'           => 'Earned flip multiple users',
                                'description'     => 'By Multiple users',
                                'earned'          => '2016-04-27T10:48:44+0000',
                                'acknowledge_id'  => '',
                                'earned_by'       => 'english_student',
                                'is_acknowledged' => true,
                                '_links'          => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/user/english_student/flip/earned-flip-multiple-users',
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
                                'flip_id'         => 'flip',
                                'title'           => 'Flip',
                                'description'     => 'This is just a flip',
                                'earned'          => '2016-04-27T10:48:45+0000',
                                'acknowledge_id'  => '2ecce58a-3fc2-11e6-8fb4-d92610f0052c',
                                'is_acknowledged' => false,
                                'earned_by'       => 'english_student',
                                '_links'          => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/user/english_student/flip/flip',
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
                    'total_items' => 2,
                    'page'        => 1,
                ],
            ],

            'GET All For child with flips to student that shares relationship' => [
                'math_student',
                '/user/english_student/flip',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_student/flip?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_student/flip',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_student/flip?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_student/flip{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'earned_flip' => [
                            [
                                'flip_id'         => 'earned-flip-multiple-users',
                                'title'           => 'Earned flip multiple users',
                                'description'     => 'By Multiple users',
                                'earned'          => '2016-04-27T10:48:44+0000',
                                'acknowledge_id'  => '',
                                'earned_by'       => 'english_student',
                                'is_acknowledged' => true,
                                '_links'          => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/user/english_student/flip/earned-flip-multiple-users',
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
                                'flip_id'         => 'flip',
                                'title'           => 'Flip',
                                'description'     => 'This is just a flip',
                                'earned'          => '2016-04-27T10:48:45+0000',
                                'acknowledge_id'  => '2ecce58a-3fc2-11e6-8fb4-d92610f0052c',
                                'is_acknowledged' => false,
                                'earned_by'       => 'english_student',
                                '_links'          => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/user/english_student/flip/flip',
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
                    'total_items' => 2,
                    'page'        => 1,
                ],
            ],

            'GET Earned for Child that is earned multiple times' => [
                'math_student',
                '/user/math_student/flip/earned-flip-multiple-times',
                200,
                [
                    '_links'      => [
                        'self'  => [
                            'href' => 'http://api.test.com/user/english_student/flip?page=1',
                        ],
                        'first' => [
                            'href' => 'http://api.test.com/user/english_student/flip',
                        ],
                        'last'  => [
                            'href' => 'http://api.test.com/user/english_student/flip?page=1',
                        ],
                        'find'  => [
                            'href'      => 'http://api.test.com/user/english_student/flip{?per_page,page}',
                            'templated' => true,
                        ],
                    ],
                    '_embedded'   => [
                        'earned_flip' => [
                            [
                                'flip_id'         => 'earned-flip-multiple-users',
                                'title'           => 'Earned flip multiple users',
                                'description'     => 'By Multiple users',
                                'earned'          => '2016-04-27T10:48:44+0000',
                                'acknowledge_id'  => '',
                                'earned_by'       => 'english_student',
                                'is_acknowledged' => true,
                                '_links'          => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/user/english_student/flip/earned-flip-multiple-users',
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
                                'flip_id'         => 'flip',
                                'title'           => 'Flip',
                                'description'     => 'This is just a flip',
                                'earned'          => '2016-04-27T10:48:45+0000',
                                'acknowledge_id'  => '2ecce58a-3fc2-11e6-8fb4-d92610f0052c',
                                'is_acknowledged' => false,
                                'earned_by'       => 'english_student',
                                '_links'          => [
                                    'self'     => [
                                        'href' => 'http://api.test.com/user/english_student/flip/flip',
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
                    'total_items' => 2,
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

}
