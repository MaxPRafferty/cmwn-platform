<?php

namespace RuleTest\Engine;

use Application\Utils\Date\DateTimeFactory;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test RulesEngineTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RulesEngineTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBuildEngineToEarnFlipOnChristmas()
    {
        $startDate = DateTimeFactory::factory('now');
        $startDate->setDate($startDate->format('Y'), '12', 25);
        $startDate->setTime(0, 0, 0);

        $endDate = clone $startDate;
        $endDate->setTime(23, 59, 59);

        $config = [
            'name' => 'Earn a flip on christmas',

            'event' => [
                'identifier' => '\\Security\\Authentication\\AuthenticationService',
                'event'      => 'login.post',
            ],

            'rules' => [
                [
                    'name'    => '\\Rule\\\Date\\\DateBetweenSpecification',
                    'options' => [
                        'start_date' => $startDate,
                        'end_date'   => $endDate,
                    ],
                ],
                [
                    'name'     => '\\Flip\\Rule\\Earned',
                    'options'  => [
                        'flip_id' => 'merry-christmas',
                    ],
                    'operator' => 'not',
                    'group'    => 'my-group',
                ],
            ],

            'actions' => [
                [
                    'name'    => '\\Flip\\Rule\\Actions\\EarnFlipAction',
                    'options' => [
                        'flip_id' => 'merry-christmas',
                        'user'    => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                    ],
                ],
                [
                    'name'    => '\\Notice\\Rule\\Actions\\NotifyFriendsAction',
                    'options' => [
                        'message-template' => 'friend-earned-flip',
                        'user'             => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                    ],
                ],
            ],

            'item_data' => [
                'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
            ],
        ];

        $config = [
            'name' => 'Friend accepted request',

            'event' => [
                'identifier' => '\\Friend\\Service\\FriendServiceInterface',
                'event'      => 'attach.friend.post',
            ],

            'rules' => [
                [
                    'name' => '\\Friend\\Rule\\FriendAccepted',
                ],
            ],

            'actions' => [
                [
                    'name'    => '\\Notice\\Rule\\Actions\\FriendAcceptedAction',
                    'options' => [
                        'message-template' => 'friend-accepted-you',
                        'user'             => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                        'friend_user'      => '\\Friend\\Rule\\Provider\\FriendedUserProvider',
                    ],
                ],
            ],

            'item_data' => [
                'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                'friend_user' => '\\Friend\\Rule\\Provider\\FriendUserProvider',
            ],
        ];

        $config = [
            'name' => 'Earn a flip when your first Friend accepts your request',

            'event' => [
                'identifier' => '\\Friend\\Service\\FriendServiceInterface',
                'event'      => 'attach.friend.post',
            ],

            'rules' => [
                [
                    'name' => '\\Friend\\Rule\\FriendAccepted',
                ],
                [
                    'name'     => '\\Flip\\Rule\\Earned',
                    'options'  => [
                        'flip_id' => 'you-got-friends',
                    ],
                    'operator' => 'not',
                ],
            ],

            'actions' => [
                [
                    'name'    => '\\Friend\\Rule\\Actions\\FriendAcceptedAction',
                    'options' => [
                        'flip_id' => 'you-got-friends',
                        'user'    => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                    ],
                ],
            ],

            'item_data' => [
                'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
            ],
        ];
    }

}
