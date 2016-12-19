<?php

namespace RuleTest\Engine;

use Api\Links\SaveGameLink;
use Application\Utils\Date\DateTimeFactory;
use Group\GroupInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use ZF\Hal\Plugin\Hal;

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

        $engineConfig = [
            [
                'id'          => 'earn-flip-on-christmas',
                'name'        => 'Earn a flip on christmas',
                'when'        => [
                    'identifier' => '\\Security\\Authentication\\AuthenticationService',
                    'event'      => 'login.post',
                ],
                'rules'       => [
                    [
                        'rule' => [
                            'name'    => '\\Rule\\\Date\\\DateBetweenSpecification',
                            'options' => [
                                'start_date' => $startDate,
                                'end_date'   => $endDate,
                            ],
                        ],
                    ],
                    [
                        'rule'     => [
                            'name'    => '\\Flip\\Rule\\Earned',
                            'options' => [
                                'flip_id' => 'merry-christmas',
                            ],
                        ],
                        'operator' => 'not',
                    ],
                ],
                'actions'     => [
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
                'item_params' => [
                    'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                    'check_user'  => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                ],
            ],
            [
                'id'          => 'notify-friend-accepted',
                'name'        => 'Friend accepted request',
                'when'        => [
                    'identifier' => '\\Friend\\Service\\FriendServiceInterface',
                    'event'      => 'attach.friend.post',
                ],
                'rules'       => [
                    [
                        'rule' => [
                            'name' => '\\Friend\\Rule\\FriendAccepted',
                        ],
                    ],
                ],
                'actions'     => [
                    [
                        'name'    => '\\Notice\\Rule\\Actions\\FriendAcceptedAction',
                        'options' => [
                            'message-template' => 'friend-accepted-you',
                            'user'             => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                            'friend_user'      => '\\Friend\\Rule\\Provider\\FriendUserProvider',
                        ],
                    ],
                ],
                'item_params' => [
                    'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                    'friend_user' => '\\Friend\\Rule\\Provider\\FriendUserProvider',
                ],
            ],
            [
                'id'          => 'earn-flip-when-friend-accepted',
                'name'        => 'Earn a flip when your first Friend accepts your request',
                'when'        => [
                    'identifier' => '\\Friend\\Service\\FriendServiceInterface',
                    'event'      => 'attach.friend.post',
                ],
                'rules'       => [
                    [
                        'rule' => [
                            'name' => '\\Friend\\Rule\\FriendAccepted',
                        ],
                    ],
                    [
                        'name'     => '\\Flip\\Rule\\Earned',
                        'options'  => [
                            'flip_id' => 'you-got-friends',
                        ],
                        'operator' => 'not',
                    ],
                ],
                'actions'     => [
                    [
                        'name'    => '\\Friend\\Rule\\Actions\\FriendAcceptedAction',
                        'options' => [
                            'flip_id' => 'you-got-friends',
                            'user'    => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                        ],
                    ],
                ],
                'item_params' => [
                    'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                ],
            ],

            [
                'id'          => 'add-save-game-hal',
                'name'        => 'Add the save game hal link',
                'when'        => [
                    'identifier' => Hal::class,
                    'event'      => 'render.entity',
                ],
                'rules'       => [
                    [
                        'rule' => [
                            'name'    => '\\Security\\Rule\\HasPermission',
                            'options' => [
                                'permission' => 'save.game',
                            ],
                        ],
                    ],
                ],
                'actions'     => [
                    [
                        'name'    => '\\Api\\Rules\\Actions\\AddLinkAction',
                        'options' => [
                            'link' => SaveGameLink::class,
                        ],
                    ],
                ],
                'item_params' => [
                    'active_user' => '\\Security\\Rule\\Provider\\ActiveUserProvider',
                ],
            ],
            [
                'id'          => 'add-group-hals',
                'name'        => 'Add the group type hal links',
                'when'        => [
                    'identifier' => Hal::class,
                    'event'      => 'render.entity',
                ],
                'rules'       => [
                    [
                        'rule' => [
                            'name'    => '\\Api\\Rule\\HalTypeRule',
                            'options' => [
                                'type' => GroupInterface::class,
                            ],
                        ],
                    ],
                ],
                'actions'     => [
                    [
                        'name' => '\\Api\\Rule\\Action\\AddGroupTypeLinks',
                    ],
                ],
                'item_params' => [
                    'group' => '\\Application\\Rule\\Provider\\HalEntityProvider',
                    'links' => '\\Application\\Rule\\Provider\\HalLinksProvider',
                ],
            ],
        ];
    }
}
