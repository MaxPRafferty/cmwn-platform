<?php
return [
    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Api\V1\Rest\Ack\AckResource::class => [
            \Flip\Service\FlipUserServiceInterface::class,
        ],
    ],
    'shared-listeners' => [
        \Api\Listeners\UserRouteListener::class,
        \Api\Listeners\UserGroupListener::class,
        \Api\Listeners\ImportRouteListener::class,
        \Api\Listeners\ScopeListener::class,
        \Api\Listeners\SuperMeListener::class,
        \Api\Listeners\UserImageListener::class,
        \Api\Listeners\ChangePasswordListener::class,
        \Api\Listeners\GroupRouteListener::class,
        \Api\Listeners\FriendListener::class,
        \Api\Listeners\UserHalLinksListener::class,
        \Api\Listeners\TemplateLinkListener::class,
        \Api\Listeners\GameRouteListener::class,
        \Api\Listeners\UserParamListener::class,
        \Api\Listeners\OrgLinkListener::class,
    ],
    'service_manager'  => [
        'factories' => [
            \Api\Listeners\ChangePasswordListener::class              =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Api\Listeners\TemplateLinkListener::class                =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Api\Listeners\UserParamListener::class                   =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Api\Listeners\OrgLinkListener::class                     =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Api\Listeners\UserHalLinksListener::class                =>
                \Api\Factory\UserHalLinksListenerFactory::class,
            \Api\Listeners\ImportRouteListener::class                 =>
                \Api\Factory\ImportRouteListenerFactory::class,
            \Api\Listeners\ScopeListener::class                       =>
                \Api\Factory\ScopeListenerFactory::class,
            \Api\Listeners\UserRouteListener::class                   =>
                \Api\Factory\UserRouteListenerFactory::class,
            \Api\Listeners\UserGroupListener::class                   =>
                \Api\Factory\UserGroupListenerFactory::class,
            \Api\Listeners\GroupRouteListener::class                  =>
                \Api\Factory\GroupRouteListenerFactory::class,
            \Api\Listeners\OrgRouteListener::class                    =>
                \Api\Factory\OrgRouteListenerFactory::class,
            \Api\Listeners\SuperMeListener::class                     =>
                \Api\Factory\SuperMeListenerFactory::class,
            \Api\Listeners\UserImageListener::class                   =>
                \Api\Factory\UserImageListenerFactory::class,
            \Api\V1\Rest\User\UserResource::class                     =>
                \Api\V1\Rest\User\UserResourceFactory::class,
            \Api\V1\Rest\Org\OrgResource::class                       =>
                \Api\V1\Rest\Org\OrgResourceFactory::class,
            \Api\V1\Rest\Game\GameResource::class                     =>
                \Api\V1\Rest\Game\GameResourceFactory::class,
            \Api\V1\Rest\Image\ImageResource::class                   =>
                \Api\V1\Rest\Image\ImageResourceFactory::class,
            \Api\V1\Rest\Group\GroupResource::class                   =>
                \Api\V1\Rest\Group\GroupResourceFactory::class,
            \Api\V1\Rest\Token\TokenResource::class                   =>
                \Api\V1\Rest\Token\TokenResourceFactory::class,
            \Api\V1\Rest\Login\LoginResource::class                   =>
                \Api\V1\Rest\Login\LoginResourceFactory::class,
            \Api\V1\Rest\Logout\LogoutResource::class                 =>
                \Api\V1\Rest\Logout\LogoutResourceFactory::class,
            \Api\V1\Rest\Forgot\ForgotResource::class                 =>
                \Api\V1\Rest\Forgot\ForgotResourceFactory::class,
            \Api\V1\Rest\Password\PasswordResource::class             =>
                \Api\V1\Rest\Password\PasswordResourceFactory::class,
            \Api\V1\Rest\GroupUsers\GroupUsersResource::class         =>
                \Api\V1\Rest\GroupUsers\GroupUsersResourceFactory::class,
            \Api\V1\Rest\OrgUsers\OrgUsersResource::class             =>
                \Api\V1\Rest\OrgUsers\OrgUsersResourceFactory::class,
            \Api\V1\Rest\UserImage\UserImageResource::class           =>
                \Api\V1\Rest\UserImage\UserImageResourceFactory::class,
            \Api\V1\Rest\UserName\UserNameResource::class             =>
                \Api\V1\Rest\UserName\UserNameResourceFactory::class,
            \Api\V1\Rest\Flip\FlipResource::class                     =>
                \Api\V1\Rest\Flip\FlipResourceFactory::class,
            \Api\V1\Rest\FlipUser\FlipUserResource::class             =>
                \Api\V1\Rest\FlipUser\FlipUserResourceFactory::class,
            \Api\V1\Rest\Friend\FriendResource::class                 =>
                \Api\V1\Rest\Friend\FriendResourceFactory::class,
            \Api\V1\Rest\Suggest\SuggestResource::class               =>
                \Api\V1\Rest\Suggest\SuggestResourceFactory::class,
            \Api\Listeners\FriendListener::class                      =>
                \Api\Factory\FriendListenerFactory::class,
            \Api\V1\Rest\Reset\ResetResource::class                   =>
                \Api\V1\Rest\Reset\ResetResourceFactory::class,
            \Api\V1\Rest\UpdatePassword\UpdatePasswordResource::class =>
                \Api\V1\Rest\UpdatePassword\UpdatePasswordResourceFactory::class,
            \Api\V1\Rest\SaveGame\SaveGameResource::class             =>
                \Api\V1\Rest\SaveGame\SaveGameResourceFactory::class,
            \Api\Listeners\GameRouteListener::class                   =>
                \Api\Factory\GameRouteListenerFactory::class,
            \Api\V1\Rest\Media\MediaResource::class                   =>
                \Api\V1\Rest\Media\MediaResourceFactory::class,
            \Api\V1\Rest\Skribble\SkribbleResource::class             =>
                \Api\V1\Rest\Skribble\SkribbleResourceFactory::class,
            \Api\V1\Rest\SkribbleNotify\SkribbleNotifyResource::class =>
                \Api\V1\Rest\SkribbleNotify\SkribbleNotifyResourceFactory::class,
            \Api\V1\Rest\Feed\FeedResource::class                     =>
                \Api\V1\Rest\Feed\FeedResourceFactory::class,
            \Api\V1\Rest\GameData\GameDataResource::class             =>
                \Api\V1\Rest\GameData\GameDataResourceFactory::class,
            \Api\V1\Rest\Flag\FlagResource::class                     =>
                \Api\V1\Rest\Flag\FlagResourceFactory::class,
            \Api\V1\Rest\GroupReset\GroupResetResource::class         =>
                \Api\V1\Rest\GroupReset\GroupResetResourceFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'api.rest.user'            => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user[/:user_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\User\Controller',
                    ],
                ],
            ],
            'api.rest.org'             => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/org[/:org_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Org\Controller',
                    ],
                ],
            ],
            'api.rest.game'            => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/game[/:game_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Game\Controller',
                    ],
                ],
            ],
            'api.rest.image'           => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/image[/:image_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Image\Controller',
                    ],
                ],
            ],
            'api.rest.group'           => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/group[/:group_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Group\Controller',
                    ],
                ],
            ],
            'api.rest.token'           => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Token\Controller',
                    ],
                ],
            ],
            'api.rest.login'           => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Login\Controller',
                    ],
                ],
            ],
            'api.rest.logout'          => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Logout\Controller',
                    ],
                ],
            ],
            'api.rest.forgot'          => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/forgot[/:forgot_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Forgot\Controller',
                    ],
                ],
            ],
            'api.rest.password'        => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/password',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Password\Controller',
                    ],
                ],
            ],
            'api.rest.group-users'     => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/group/:group_id/users',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\GroupUsers\Controller',
                    ],
                ],
            ],
            'api.rest.org-users'       => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/org/:org_id/users',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\OrgUsers\Controller',
                    ],
                ],
            ],
            'api.rest.user-image'      => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/image',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\UserImage\Controller',
                    ],
                ],
            ],
            'api.rest.user-name'       => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user-name[/:user_name_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\UserName\Controller',
                    ],
                ],
            ],
            'api.rest.flip'            => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/flip[/:flip_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Flip\Controller',
                    ],
                ],
            ],
            'api.rest.flip-user'       => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/flip[/:flip_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\FlipUser\Controller',
                    ],
                ],
            ],
            'api.rest.friend'          => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/friend[/:friend_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Friend\Controller',
                    ],
                ],
            ],
            'api.rest.suggest'         => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/suggest[/:suggest_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Suggest\Controller',
                    ],
                ],
            ],
            'api.rest.reset'           => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/reset[/:reset_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Reset\Controller',
                    ],
                ],
            ],
            'api.rest.update-password' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/password[/:update_password_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\UpdatePassword\Controller',
                    ],
                ],
            ],
            'api.rest.save-game'       => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/game[/:game_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\SaveGame\Controller',
                    ],
                ],
            ],
            'api.rest.game-data'       => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/game-data[/:game_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\GameData\Controller',
                    ],
                ],
            ],
            'api.rest.media'           => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/media[/:media_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Media\Controller',
                    ],
                ],
            ],
            'api.rest.skribble'        => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/skribble[/:skribble_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Skribble\Controller',
                    ],
                ],
            ],
            'api.rest.skribble-notify' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/skribble/:skribble_id/notice',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\SkribbleNotify\Controller',
                    ],
                ],
            ],
            'api.rest.feed'            => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:user_id/feed[/:feed_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Feed\Controller',
                    ],
                ],
            ],
            'api.rest.flag'            => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/flag[/:flag_id]',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Flag\Controller',
                    ],
                ],
            ],
            'api.rest.group-reset'     => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/group/:group_id/reset',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\GroupReset\Controller',
                    ],
                ],
            ],
            'api.rest.acknowledge'     => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/ack/:ack_id',
                    'defaults' => [
                        'controller' => 'Api\V1\Rest\Ack\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning'          => [
        'uri' => [
            'api.rest.user',
            'api.rest.org',
            'api.rest.game',
            'api.rest.image',
            'api.rest.group',
            'api.rest.token',
            'api.rest.login',
            'api.rest.logout',
            'api.rest.forgot',
            'api.rest.password',
            'api.rest.group-users',
            'api.rest.org-users',
            'api.rest.user-image',
            'api.rest.user-name',
            'api.rest.flip',
            'api.rest.flip-user',
            'api.rest.friend',
            'api.rest.suggest',
            'api.rest.reset',
            'api.rest.update-password',
            'api.rest.save-game',
            'api.rest.media',
            'api.rest.skribble',
            'api.rest.skribble-notify',
            'api.rest.feed',
            'api.rest.game-data',
            'api.rest.flag',
            'api.rest.group-reset',
            'api.rest.acknowledge',
        ],
    ],
    'zf-rest'                => [
        'Api\V1\Rest\User\Controller'           => [
            'listener'                   => \Api\V1\Rest\User\UserResource::class,
            'route_name'                 => 'api.rest.user',
            'route_identifier_name'      => 'user_id',
            'collection_name'            => 'user',
            'entity_http_methods'        => ['GET', 'PUT', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => ['type', 'page', 'per_page', 'deleted', 'username'],
            'page_size'                  => 100,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\User\UserEntity::class,
            'collection_class'           => \Api\V1\Rest\User\UserCollection::class,
            'service_name'               => 'User',
        ],
        'Api\V1\Rest\Org\Controller'            => [
            'listener'                   => \Api\V1\Rest\Org\OrgResource::class,
            'route_name'                 => 'api.rest.org',
            'route_identifier_name'      => 'org_id',
            'collection_name'            => 'org',
            'entity_http_methods'        => ['GET', 'PUT', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => ['page', 'per_page', 'type'],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Org\OrgEntity::class,
            'collection_class'           => \Api\V1\Rest\Org\OrgCollection::class,
            'service_name'               => 'Org',
        ],
        'Api\V1\Rest\Game\Controller'           => [
            'listener'                   => \Api\V1\Rest\Game\GameResource::class,
            'route_name'                 => 'api.rest.game',
            'route_identifier_name'      => 'game_id',
            'collection_name'            => 'game',
            'entity_http_methods'        => ['GET', 'PUT', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => ['page', 'per_page'],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Game\GameEntity::class,
            'collection_class'           => \Api\V1\Rest\Game\GameCollection::class,
            'service_name'               => 'Game',
        ],
        'Api\V1\Rest\Image\Controller'          => [
            'listener'                   => \Api\V1\Rest\Image\ImageResource::class,
            'route_name'                 => 'api.rest.image',
            'route_identifier_name'      => 'image_id',
            'collection_name'            => 'image',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'page',
            'entity_class'               => \Api\V1\Rest\Image\ImageEntity::class,
            'collection_class'           => \Api\V1\Rest\Image\ImageCollection::class,
            'service_name'               => 'Image',
        ],
        'Api\V1\Rest\Group\Controller'          => [
            'listener'                   => \Api\V1\Rest\Group\GroupResource::class,
            'route_name'                 => 'api.rest.group',
            'route_identifier_name'      => 'group_id',
            'collection_name'            => 'group',
            'entity_http_methods'        => ['GET', 'PUT', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => ['page', 'per_page', 'type', 'parent', 'org_id'],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Group\GroupEntity::class,
            'collection_class'           => \Api\V1\Rest\Group\GroupCollection::class,
            'service_name'               => 'Group',
        ],
        'Api\V1\Rest\Token\Controller'          => [
            'listener'                   => \Api\V1\Rest\Token\TokenResource::class,
            'route_name'                 => 'api.rest.token',
            'route_identifier_name'      => 'token_id',
            'collection_name'            => 'token',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Token\TokenEntity::class,
            'collection_class'           => \Api\V1\Rest\Token\TokenCollection::class,
            'service_name'               => 'Token',
        ],
        'Api\V1\Rest\Login\Controller'          => [
            'listener'                   => \Api\V1\Rest\Login\LoginResource::class,
            'route_name'                 => 'api.rest.login',
            'route_identifier_name'      => 'login_id',
            'collection_name'            => 'login',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Login\LoginEntity::class,
            'collection_class'           => \Api\V1\Rest\Login\LoginCollection::class,
            'service_name'               => 'login',
        ],
        'Api\V1\Rest\Logout\Controller'         => [
            'listener'                   => \Api\V1\Rest\Logout\LogoutResource::class,
            'route_name'                 => 'api.rest.logout',
            'route_identifier_name'      => 'logout_id',
            'collection_name'            => 'logout',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Logout\LogoutEntity::class,
            'collection_class'           => \Api\V1\Rest\Logout\LogoutCollection::class,
            'service_name'               => 'Logout',
        ],
        'Api\V1\Rest\Forgot\Controller'         => [
            'listener'                   => \Api\V1\Rest\Forgot\ForgotResource::class,
            'route_name'                 => 'api.rest.forgot',
            'route_identifier_name'      => 'forgot_id',
            'collection_name'            => 'forgot',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Forgot\ForgotEntity::class,
            'collection_class'           => \Api\V1\Rest\Forgot\ForgotCollection::class,
            'service_name'               => 'Forgot',
        ],
        'Api\V1\Rest\Password\Controller'       => [
            'listener'                   => \Api\V1\Rest\Password\PasswordResource::class,
            'route_name'                 => 'api.rest.password',
            'route_identifier_name'      => 'user_id',
            'collection_name'            => 'password',
            // TODO update to only take in post to /user/:user_id/password
            'entity_http_methods'        => ['POST'],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Password\PasswordEntity::class,
            'collection_class'           => \Api\V1\Rest\Password\PasswordCollection::class,
            'service_name'               => 'Password',
        ],
        'Api\V1\Rest\GroupUsers\Controller'     => [
            'listener'                   => \Api\V1\Rest\GroupUsers\GroupUsersResource::class,
            'route_name'                 => 'api.rest.group-users',
            'route_identifier_name'      => 'foo_bar_id',
            'collection_name'            => 'items',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\GroupUsers\GroupUsersCollection::class,
            'collection_class'           => \Api\V1\Rest\GroupUsers\GroupUsersCollection::class,
            'service_name'               => 'GroupUsers',
        ],
        'Api\V1\Rest\OrgUsers\Controller'       => [
            'listener'                   => \Api\V1\Rest\OrgUsers\OrgUsersResource::class,
            'route_name'                 => 'api.rest.org-users',
            'route_identifier_name'      => 'foo_org_id',
            'collection_name'            => 'items',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\OrgUsers\OrgUsersEntity::class,
            'collection_class'           => \Api\V1\Rest\OrgUsers\OrgUsersCollection::class,
            'service_name'               => 'OrgUsers',
        ],
        'Api\V1\Rest\UserImage\Controller'      => [
            'listener'                   => \Api\V1\Rest\UserImage\UserImageResource::class,
            'route_name'                 => 'api.rest.user-image',
            'route_identifier_name'      => 'user_id',
            'collection_name'            => 'user_image',
            'entity_http_methods'        => ['POST', 'GET'],
            'collection_http_methods'    => [],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\UserImage\UserImageEntity::class,
            'collection_class'           => \Api\V1\Rest\UserImage\UserImageCollection::class,
            'service_name'               => 'UserImage',
        ],
        'Api\V1\Rest\UserName\Controller'       => [
            'listener'                   => \Api\V1\Rest\UserName\UserNameResource::class,
            'route_name'                 => 'api.rest.user-name',
            'route_identifier_name'      => 'user_name_id',
            'collection_name'            => 'user_name',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\UserName\UserNameEntity::class,
            'collection_class'           => \Api\V1\Rest\UserName\UserNameCollection::class,
            'service_name'               => 'UserName',
        ],
        'Api\V1\Rest\Flip\Controller'           => [
            'listener'                   => \Api\V1\Rest\Flip\FlipResource::class,
            'route_name'                 => 'api.rest.flip',
            'route_identifier_name'      => 'flip_id',
            'collection_name'            => 'flip',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'page',
            'entity_class'               => \Api\V1\Rest\Flip\FlipEntity::class,
            'collection_class'           => \Api\V1\Rest\Flip\FlipCollection::class,
            'service_name'               => 'Flip',
        ],
        'Api\V1\Rest\FlipUser\Controller'       => [
            'listener'                   => \Api\V1\Rest\FlipUser\FlipUserResource::class,
            'route_name'                 => 'api.rest.flip-user',
            'route_identifier_name'      => 'flip_id',
            'collection_name'            => 'flip_user',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\FlipUser\FlipUserEntity::class,
            'collection_class'           => \Api\V1\Rest\FlipUser\FlipUserCollection::class,
            'service_name'               => 'FlipUser',
        ],
        'Api\V1\Rest\Friend\Controller'         => [
            'listener'                   => \Api\V1\Rest\Friend\FriendResource::class,
            'route_name'                 => 'api.rest.friend',
            'route_identifier_name'      => 'friend_id',
            'collection_name'            => 'friend',
            'entity_http_methods'        => ['GET', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Friend\FriendEntity::class,
            'collection_class'           => \Api\V1\Rest\Friend\FriendCollection::class,
            'service_name'               => 'friend',
        ],
        'Api\V1\Rest\Suggest\Controller'        => [
            'listener'                   => \Api\V1\Rest\Suggest\SuggestResource::class,
            'route_name'                 => 'api.rest.suggest',
            'route_identifier_name'      => 'suggest_id',
            'collection_name'            => 'suggest',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Suggest\SuggestEntity::class,
            'collection_class'           => \Api\V1\Rest\Suggest\SuggestionCollection::class,
            'service_name'               => 'Suggest',
        ],
        'Api\V1\Rest\Reset\Controller'          => [
            'listener'                   => \Api\V1\Rest\Reset\ResetResource::class,
            'route_name'                 => 'api.rest.reset',
            'route_identifier_name'      => 'reset_id',
            'collection_name'            => 'reset',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Reset\ResetEntity::class,
            'collection_class'           => \Api\V1\Rest\Reset\ResetCollection::class,
            'service_name'               => 'Reset',
        ],
        'Api\V1\Rest\UpdatePassword\Controller' => [
            'listener'                   => \Api\V1\Rest\UpdatePassword\UpdatePasswordResource::class,
            'route_name'                 => 'api.rest.update-password',
            'route_identifier_name'      => 'update_password_id',
            'collection_name'            => 'update_password',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\UpdatePassword\UpdatePasswordEntity::class,
            'collection_class'           => \Api\V1\Rest\UpdatePassword\UpdatePasswordCollection::class,
            'service_name'               => 'UpdatePassword',
        ],
        'Api\V1\Rest\SaveGame\Controller'       => [
            'listener'                   => \Api\V1\Rest\SaveGame\SaveGameResource::class,
            'route_name'                 => 'api.rest.save-game',
            'route_identifier_name'      => 'game_id',
            'collection_name'            => 'save_game',
            'entity_http_methods'        => ['DELETE', 'GET', 'POST'],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\SaveGame\SaveGameEntity::class,
            'collection_class'           => \Api\V1\Rest\SaveGame\SaveGameCollection::class,
            'service_name'               => 'SaveGame',
        ],
        'Api\V1\Rest\Media\Controller'          => [
            'listener'                   => \Api\V1\Rest\Media\MediaResource::class,
            'route_name'                 => 'api.rest.media',
            'route_identifier_name'      => 'media_id',
            'collection_name'            => 'media',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => null,
            'entity_class'               => \Api\V1\Rest\Media\MediaEntity::class,
            'collection_class'           => \Api\V1\Rest\Media\MediaCollection::class,
            'service_name'               => 'Media',
        ],
        'Api\V1\Rest\Skribble\Controller'       => [
            'listener'                   => \Api\V1\Rest\Skribble\SkribbleResource::class,
            'route_name'                 => 'api.rest.skribble',
            'route_identifier_name'      => 'skribble_id',
            'collection_name'            => 'skribble',
            'entity_http_methods'        => ['GET', 'PATCH', 'PUT', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => ['page', 'per_page', 'status', 'read'],
            'page_size'                  => 100,
            'page_size_param'            => null,
            'entity_class'               => \Api\V1\Rest\Skribble\SkribbleEntity::class,
            'collection_class'           => \Api\V1\Rest\Skribble\SkribbleCollection::class,
            'service_name'               => 'Skribble',
        ],
        'Api\V1\Rest\SkribbleNotify\Controller' => [
            'listener'                   => \Api\V1\Rest\SkribbleNotify\SkribbleNotifyResource::class,
            'route_name'                 => 'api.rest.skribble-notify',
            'route_identifier_name'      => 'skribble_id',
            'collection_name'            => 'skribble_notify',
            'entity_http_methods'        => ['POST'],
            'collection_http_methods'    => [],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => null,
            'entity_class'               => \Api\V1\Rest\SkribbleNotify\SkribbleNotifyEntity::class,
            'collection_class'           => \Api\V1\Rest\SkribbleNotify\SkribbleNotifyCollection::class,
            'service_name'               => 'SkribbleNotify',
        ],
        'Api\V1\Rest\Feed\Controller'           => [
            'listener'                   => \Api\V1\Rest\Feed\FeedResource::class,
            'route_name'                 => 'api.rest.feed',
            'route_identifier_name'      => 'feed_id',
            'collection_name'            => 'feed',
            'entity_http_methods'        => [],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => ['page', 'per_page'],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\Feed\FeedEntity::class,
            'collection_class'           => \Api\V1\Rest\Feed\FeedCollection::class,
            'service_name'               => 'Feed',
        ],
        'Api\V1\Rest\GameData\Controller'       => [
            'listener'                   => \Api\V1\Rest\GameData\GameDataResource::class,
            'route_name'                 => 'api.rest.game-data',
            'route_identifier_name'      => 'game_id',
            'collection_name'            => 'game-data',
            'entity_http_methods'        => ['GET'],
            'collection_http_methods'    => ['GET'],
            'collection_query_whitelist' => ['page', 'per_page'],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\GameData\GameDataEntity::class,
            'collection_class'           => \Api\V1\Rest\GameData\GameDataCollection::class,
            'service_name'               => 'GameData',
        ],
        'Api\V1\Rest\Flag\Controller'           => [
            'listener'                   => \Api\V1\Rest\Flag\FlagResource::class,
            'route_name'                 => 'api.rest.flag',
            'route_identifier_name'      => 'flag_id',
            'collection_name'            => 'flags',
            'entity_http_methods'        => ['GET', 'PUT', 'DELETE'],
            'collection_http_methods'    => ['GET', 'POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'page',
            'entity_class'               => \Api\V1\Rest\Flag\FlagEntity::class,
            'collection_class'           => \Api\V1\Rest\Flag\FlagCollection::class,
            'service_name'               => 'Flag',
        ],
        'Api\V1\Rest\GroupReset\Controller'     => [
            'listener'                   => \Api\V1\Rest\GroupReset\GroupResetResource::class,
            'route_name'                 => 'api.rest.group-reset',
            'route_identifier_name'      => 'group_id',
            'collection_name'            => 'group-reset',
            'entity_http_methods'        => ['POST'],
            'collection_http_methods'    => ['POST'],
            'collection_query_whitelist' => [],
            'page_size'                  => 25,
            'page_size_param'            => 'per_page',
            'entity_class'               => \Api\V1\Rest\GroupReset\GroupResetEntity::class,
            'collection_class'           => \Api\V1\Rest\GroupReset\GroupResetCollection::class,
            'service_name'               => 'GroupReset',
        ],
        'Api\V1\Rest\Ack\Controller'     => [
            'listener'                   => \Api\V1\Rest\Ack\AckResource::class,
            'route_name'                 => 'api.rest.acknowledge',
            'route_identifier_name'      => 'ack_id',
            'collection_name'            => 'acknowledge',
            'entity_http_methods'        => ['PUT'],
            'service_name'               => 'AckFlip',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers'            => [
            'Api\V1\Rest\User\Controller'           => 'HalJson',
            'Api\V1\Rest\Org\Controller'            => 'HalJson',
            'Api\V1\Rest\Game\Controller'           => 'HalJson',
            'Api\V1\Rest\Image\Controller'          => 'HalJson',
            'Api\V1\Rest\Group\Controller'          => 'HalJson',
            'Api\V1\Rest\Token\Controller'          => 'HalJson',
            'Api\V1\Rest\Login\Controller'          => 'HalJson',
            'Api\V1\Rest\Logout\Controller'         => 'HalJson',
            'Api\V1\Rest\Forgot\Controller'         => 'HalJson',
            'Api\V1\Rest\Password\Controller'       => 'HalJson',
            'Api\V1\Rest\GroupUsers\Controller'     => 'HalJson',
            'Api\V1\Rest\OrgUsers\Controller'       => 'HalJson',
            'Api\V1\Rest\UserImage\Controller'      => 'HalJson',
            'Api\V1\Rest\UserName\Controller'       => 'HalJson',
            'Api\V1\Rest\Flip\Controller'           => 'HalJson',
            'Api\V1\Rest\FlipUser\Controller'       => 'HalJson',
            'Api\V1\Rest\Friend\Controller'         => 'HalJson',
            'Api\V1\Rest\Suggest\Controller'        => 'HalJson',
            'Api\V1\Rest\Reset\Controller'          => 'HalJson',
            'Api\V1\Rest\UpdatePassword\Controller' => 'HalJson',
            'Api\V1\Rest\SaveGame\Controller'       => 'HalJson',
            'Api\V1\Rest\Media\Controller'          => 'HalJson',
            'Api\V1\Rest\Skribble\Controller'       => 'HalJson',
            'Api\V1\Rest\SkribbleNotify\Controller' => 'HalJson',
            'Api\V1\Rest\Feed\Controller'           => 'HalJson',
            'Api\V1\Rest\GameData\Controller'       => 'HalJson',
            'Api\V1\Rest\Flag\Controller'           => 'HalJson',
            'Api\V1\Rest\GroupReset\Controller'     => 'HalJson',
        ],
        'accept_whitelist'       => [
            'Api\V1\Rest\User\Controller'           => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Org\Controller'            => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Game\Controller'           => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Image\Controller'          => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Group\Controller'          => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Token\Controller'          => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Login\Controller'          => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Logout\Controller'         => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Forgot\Controller'         => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Password\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\GroupUsers\Controller'     => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\OrgUsers\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\UserImage\Controller'      => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\UserName\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Flip\Controller'           => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\FlipUser\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Friend\Controller'         => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Suggest\Controller'        => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Reset\Controller'          => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\UpdatePassword\Controller' => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\SaveGame\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Media\Controller'          => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Skribble\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\SkribbleNotify\Controller' => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\Feed\Controller'           => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\GameData\Controller'       => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
            'Api\V1\Rest\GroupReset\Controller'     => [
                'application/vnd.api.v1+json',
                'application/hal+json',
                'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'Api\V1\Rest\User\Controller'           => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Org\Controller'            => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Game\Controller'           => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Image\Controller'          => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Group\Controller'          => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Token\Controller'          => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Login\Controller'          => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Logout\Controller'         => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Forgot\Controller'         => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Password\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\GroupUsers\Controller'     => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\OrgUsers\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\UserImage\Controller'      => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\UserName\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Flip\Controller'           => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\FlipUser\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Suggest\Controller'        => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Friend\Controller'         => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Reset\Controller'          => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\UpdatePassword\Controller' => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\SaveGame\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Media\Controller'          => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Skribble\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\SkribbleNotify\Controller' => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Feed\Controller'           => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\GameData\Controller'       => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\Flag\Controller'           => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
            'Api\V1\Rest\GroupReset\Controller'     => [
                'application/vnd.api.v1+json',
                'application/json',
            ],
        ],
    ],
    'zf-hal'                 => [
        'metadata_map' => [
            \Api\V1\Rest\User\UserEntity::class                         => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.user',
                'route_identifier_name'  => 'user_id',
                'max_depth'              => 2,
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\User\MeEntity::class                           => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.user',
                'route_identifier_name'  => 'user_id',
                'max_depth'              => 2,
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\User\UserCollection::class                     => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.user',
                'route_identifier_name'  => 'user_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Org\OrgEntity::class                           => [
                'entity_identifier_name' => 'org_id',
                'route_name'             => 'api.rest.org',
                'route_identifier_name'  => 'org_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Org\OrgCollection::class                       => [
                'entity_identifier_name' => 'org_id',
                'route_name'             => 'api.rest.org',
                'route_identifier_name'  => 'org_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Game\GameEntity::class                         => [
                'entity_identifier_name' => 'game_id',
                'route_name'             => 'api.rest.game',
                'route_identifier_name'  => 'game_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Game\GameCollection::class                     => [
                'entity_identifier_name' => 'game_id',
                'route_name'             => 'api.rest.game',
                'route_identifier_name'  => 'game_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Image\ImageEntity::class                       => [
                'entity_identifier_name' => 'image_id',
                'route_name'             => 'api.rest.image',
                'route_identifier_name'  => 'image_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Image\ImageCollection::class                   => [
                'entity_identifier_name' => 'image_id',
                'route_name'             => 'api.rest.image',
                'route_identifier_name'  => 'image_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Group\GroupEntity::class                       => [
                'entity_identifier_name' => 'group_id',
                'route_name'             => 'api.rest.group',
                'route_identifier_name'  => 'group_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Group\GroupCollection::class                   => [
                'entity_identifier_name' => 'group_id',
                'route_name'             => 'api.rest.group',
                'route_identifier_name'  => 'group_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Token\TokenEntity::class                       => [
                'entity_identifier_name' => 'token',
                'route_name'             => 'api.rest.token',
                'route_identifier_name'  => 'token_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Login\LoginEntity::class                       => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.login',
                'route_identifier_name'  => 'login_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Login\LoginCollection::class                   => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.login',
                'route_identifier_name'  => 'login_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Logout\LogoutEntity::class                     => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.logout',
                'route_identifier_name'  => 'logout_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Logout\LogoutCollection::class                 => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.logout',
                'route_identifier_name'  => 'logout_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Forgot\ForgotEntity::class                     => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.forgot',
                'route_identifier_name'  => 'forgot_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Forgot\ForgotCollection::class                 => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.forgot',
                'route_identifier_name'  => 'forgot_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Password\PasswordEntity::class                 => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.password',
                'route_identifier_name'  => 'user_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Password\PasswordCollection::class             => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.password',
                'route_identifier_name'  => 'user_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\GroupUsers\GroupUsersEntity::class             => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.group-users',
                'route_identifier_name'  => 'group_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\GroupUsers\GroupUsersCollection::class         => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.group-users',
                'route_identifier_name'  => 'group_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\OrgUsers\OrgUsersEntity::class                 => [
                'entity_identifier_name' => 'org_id',
                'route_name'             => 'api.rest.org-users',
                'route_identifier_name'  => 'org_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\OrgUsers\OrgUsersCollection::class             => [
                'entity_identifier_name' => 'org_id',
                'route_name'             => 'api.rest.org-users',
                'route_identifier_name'  => 'org_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\UserImage\UserImageEntity::class               => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.user-image',
                'route_identifier_name'  => 'user_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\UserImage\UserImageCollection::class           => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.user-image',
                'route_identifier_name'  => 'user_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\UserName\UserNameEntity::class                 => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.user-name',
                'route_identifier_name'  => 'user_name_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\UserName\UserNameCollection::class             => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.user-name',
                'route_identifier_name'  => 'user_name_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Flip\FlipEntity::class                         => [
                'entity_identifier_name' => 'flip_id',
                'route_name'             => 'api.rest.flip',
                'route_identifier_name'  => 'flip_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Flip\FlipCollection::class                     => [
                'entity_identifier_name' => 'flip_id',
                'route_name'             => 'api.rest.flip',
                'route_identifier_name'  => 'flip_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\FlipUser\FlipUserEntity::class                 => [
                'entity_identifier_name' => 'flip_id',
                'route_name'             => 'api.rest.flip-user',
                'route_identifier_name'  => 'flip_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\FlipUser\FlipUserCollection::class             => [
                'entity_identifier_name' => 'flip_id',
                'route_name'             => 'api.rest.flip-user',
                'route_identifier_name'  => 'flip_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Friend\FriendEntity::class                     => [
                'entity_identifier_name' => 'friend_id',
                'route_name'             => 'api.rest.friend',
                'route_identifier_name'  => 'friend_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Friend\FriendCollection::class                 => [
                'entity_identifier_name' => 'friend_id',
                'route_name'             => 'api.rest.friend',
                'route_identifier_name'  => 'friend_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Suggest\SuggestEntity::class                   => [
                'entity_identifier_name' => 'suggest_id',
                'route_name'             => 'api.rest.suggest',
                'route_identifier_name'  => 'suggest_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Suggest\SuggestCollection::class               => [
                'entity_identifier_name' => 'suggest_id',
                'route_name'             => 'api.rest.suggest',
                'route_identifier_name'  => 'suggest_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Reset\ResetEntity::class                       => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.reset',
                'route_identifier_name'  => 'reset_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Reset\ResetCollection::class                   => [
                'entity_identifier_name' => 'user_id',
                'route_name'             => 'api.rest.reset',
                'route_identifier_name'  => 'reset_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\UpdatePassword\UpdatePasswordEntity::class     => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.update-password',
                'route_identifier_name'  => 'update_password_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\UpdatePassword\UpdatePasswordCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name'             => 'api.rest.update-password',
                'route_identifier_name'  => 'update_password_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\SaveGame\SaveGameEntity::class                 => [
                'entity_identifier_name' => 'game_id',
                'route_name'             => 'api.rest.save-game',
                'route_identifier_name'  => 'game_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\SaveGame\SaveGameCollection::class             => [
                'entity_identifier_name' => 'game_id',
                'route_name'             => 'api.rest.save-game',
                'route_identifier_name'  => 'game_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Media\MediaEntity::class                       => [
                'entity_identifier_name' => 'media_id',
                'route_name'             => 'api.rest.media',
                'route_identifier_name'  => 'media_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Media\MediaCollection::class                   => [
                'entity_identifier_name' => 'media_id',
                'route_name'             => 'api.rest.media',
                'route_identifier_name'  => 'media_id',
                'is_collection'          => true,
            ],
            'Media\MediaCollection'                                     => [
                'entity_identifier_name' => 'media_id',
                'route_name'             => 'api.rest.media',
                'route_identifier_name'  => 'media_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Skribble\SkribbleEntity::class                 => [
                'entity_identifier_name' => 'skribble_id',
                'route_name'             => 'api.rest.skribble',
                'route_identifier_name'  => 'skribble_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Skribble\SkribbleCollection::class             => [
                'entity_identifier_name' => 'skribble_id',
                'route_name'             => 'api.rest.skribble',
                'route_identifier_name'  => 'skribble_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\SkribbleNotify\SkribbleNotifyEntity::class     => [
                'entity_identifier_name' => 'skribble_id',
                'route_name'             => 'api.rest.skribble-notify',
                'route_identifier_name'  => 'skribble_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\SkribbleNotify\SkribbleNotifyCollection::class => [
                'entity_identifier_name' => 'skribble_id',
                'route_name'             => 'api.rest.skribble-notify',
                'route_identifier_name'  => 'skribble_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Feed\FeedEntity::class                         => [
                'route_name'            => 'api.rest.feed',
                'route_identifier_name' => 'feed_id',
                'hydrator'              => \Zend\Hydrator\ArraySerializable::class,
                'max_depth'             => 3,
                //TODO Add a test to check if the image of the semder is correctly sent
            ],
            \Api\V1\Rest\Feed\FeedCollection::class                     => [
                'entity_identifier_name' => 'feed_id',
                'route_name'             => 'api.rest.feed',
                'route_identifier_name'  => 'feed_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Feed\SenderEntity::class                       => [
                'route_name'            => 'api.rest.feed',
                'route_identifier_name' => 'feed_id',
                'hydrator'              => \Zend\Hydrator\ArraySerializable::class,
                'max_depth'             => 2,
            ],
            \Api\V1\Rest\GameData\GameDataEntity::class                 => [
                'route_name'            => 'api.rest.game-data',
                'route_identifier_name' => 'game_id',
                'hydrator'              => \Zend\Hydrator\ArraySerializable::class,
                'max_depth'             => 2,
            ],
            \Api\V1\Rest\GameData\GameDataCollection::class             => [
                'entity_identifier_name' => 'game_id',
                'route_name'             => 'api.rest.game-data',
                'route_identifier_name'  => 'game_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\Flag\FlagEntity::class                         => [
                'entity_identifier_name' => 'flag_id',
                'route_name'             => 'api.rest.flag',
                'route_identifier_name'  => 'flag_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\Flag\FlagCollection::class                     => [
                'entity_identifier_name' => 'flag_id',
                'route_name'             => 'api.rest.flag',
                'route_identifier_name'  => 'flag_id',
                'is_collection'          => true,
            ],
            \Api\V1\Rest\GroupReset\GroupResetEntity::class             => [
                'entity_identifier_name' => 'group_id',
                'route_name'             => 'api.rest.group-reset',
                'route_identifier_name'  => 'group_id',
                'hydrator'               => \Zend\Hydrator\ArraySerializable::class,
            ],
            \Api\V1\Rest\GroupReset\GroupResetCollection::class         => [
                'entity_identifier_name' => 'group_id',
                'route_name'             => 'api.rest.group-reset',
                'route_identifier_name'  => 'group_id',
                'is_collection'          => true,
            ],
        ],
    ],
    'zf-content-validation'  => [
        'Api\V1\Rest\User\Controller'           => [
            'input_filter' => 'Api\V1\Rest\User\Validator',
        ],
        'Api\V1\Rest\Org\Controller'            => [
            'input_filter' => 'Api\V1\Rest\Org\Validator',
        ],
        'Api\V1\Rest\Image\Controller'          => [
            'input_filter' => 'Api\V1\Rest\Image\Validator',
        ],
        'Api\V1\Rest\Group\Controller'          => [
            'input_filter' => 'Api\V1\Rest\Group\Validator',
        ],
        'Api\V1\Rest\Login\Controller'          => [
            'input_filter' => 'Api\V1\Rest\Login\Validator',
        ],
        'Api\V1\Rest\UserImage\Controller'      => [
            'input_filter' => 'Api\V1\Rest\UserImage\Validator',
        ],
        'Api\V1\Rest\Password\Controller'       => [
            'input_filter' => 'Api\V1\Rest\Password\Validator',
        ],
        'Api\V1\Rest\Forgot\Controller'         => [
            'input_filter' => 'Api\V1\Rest\Forgot\Validator',
        ],
        'Api\V1\Rest\UserName\Controller'       => [
            'input_filter' => 'Api\V1\Rest\UserName\Validator',
        ],
        'Api\V1\Rest\FlipUser\Controller'       => [
            'input_filter' => 'Api\V1\Rest\FlipUser\Validator',
        ],
        'Api\V1\Rest\Friend\Controller'         => [
            'input_filter' => 'Api\V1\Rest\Friend\Validator',
        ],
        'Api\V1\Rest\Suggest\Controller'        => [
            'input_filter' => 'Api\V1\Rest\Suggest\Validator',
        ],
        'Api\V1\Rest\Reset\Controller'          => [
            'input_filter' => 'Api\V1\Rest\Reset\Validator',
        ],
        'Api\V1\Rest\UpdatePassword\Controller' => [
            'input_filter' => 'Api\V1\Rest\UpdatePassword\Validator',
        ],
        'Api\V1\Rest\SaveGame\Controller'       => [
            'input_filter' => 'Api\V1\Rest\SaveGame\Validator',
        ],
        'Api\V1\Rest\Skribble\Controller'       => [
            'input_filter' => 'Api\V1\Rest\Skribble\Validator',
        ],
        'Api\V1\Rest\SkribbleNotify\Controller' => [
            'input_filter' => 'Api\V1\Rest\SkribbleNotify\Validator',
        ],
        'Api\V1\Rest\Flag\Controller'           => [
            'input_filter' => 'Api\V1\Rest\Flag\Validator',
        ],
        'Api\V1\Rest\Game\Controller'           => [
            'input_filter' => 'Api\V1\Rest\Game\Validator',
        ],
        'Api\V1\Rest\GroupReset\Controller'     => [
            'input_filter' => 'Api\V1\Rest\GroupReset\Validator',
        ],
    ],
    'input_filter_specs'     => [
        'Api\V1\Rest\User\Validator'           => [
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'first_name',
                'description'   => 'Users First name',
                'error_message' => 'First name is invalid',
            ],
            [
                'required'      => false,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'middle_name',
                'description'   => 'Users Middle Name',
                'error_message' => 'Middle name is invalid',
            ],
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'last_name',
                'description'   => 'Users Last name',
                'error_message' => 'Last name is invalid',
            ],
            [
                'required'      => false,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'gender',
                'description'   => 'Users Gender',
                'error_message' => 'Invalid Gender',
            ],
            [
                'required'    => false,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Application\Utils\MetaFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'meta',
                'description' => 'meta data',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \User\TypeValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [],
                'name'        => 'type',
                'description' => 'The type of user',
            ],
            [
                'required'      => false,
                'validators'    => [
                    [
                        'name'    => \User\UpdateUsernameValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'       => [],
                'name'          => 'username',
                'description'   => 'Users name',
                'error_message' => 'Invalid Username',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\EmailAddress::class,
                        'options' => [],
                    ],
                    [
                        'name'    => \User\UpdateEmailValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'       => [],
                'name'          => 'email',
                'description'   => 'Users Email',
                'error_message' => 'Invalid Email',
            ],
            [
                'required'      => false,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\Date::class,
                        'options' => [],
                    ],
                ],
                'filters'       => [],
                'name'          => 'birthdate',
                'description'   => 'birthdate',
                'error_message' => 'Invalid Birthdate',
            ],
        ],
        'Api\V1\Rest\Org\Validator'            => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '255',
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'title',
                'description' => 'Organizations title',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '255',
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'description',
                'description' => 'Organizations description',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '255',
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'type',
                'description' => 'Type of organization',
            ],
            [
                'required'    => false,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Application\Utils\MetaFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'meta',
                'description' => 'Meta data for the organization',
            ],
        ],
        'Api\V1\Rest\Image\Validator'          => [
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'public_id',
                'description'   => 'The Image Id',
                'error_message' => 'Invalid Image Id',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                'moderation',
                            ],
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'notification_type',
                'description'   => 'Notification type',
                'error_message' => 'Invalid Notification Type',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                'approved',
                                'rejected',
                                'pending',
                            ],
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'moderation_status',
                'description'   => 'Moderation status',
                'error_message' => 'Invalid Moderation status',
            ],
        ],
        'Api\V1\Rest\Group\Validator'          => [
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '255',
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'organization_id',
                'description'   => 'The Id of the organization this group belongs too',
                'error_message' => 'Invalid Organization or not found',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '255',
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'title',
                'description'   => 'The title for this Organization',
                'error_message' => 'Invalid Title',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => '255',
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'description',
                'description'   => 'Organizations description',
                'error_message' => 'Invalid Description',
            ],
            [
                'required'      => false,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'meta',
                'description'   => 'Meta data for the group',
                'error_message' => 'Invalid Meta data',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Application\Utils\TypeValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'       => [],
                'name'          => 'type',
                'description'   => 'Type of group',
                'error_message' => 'Invalid group type',
            ],
        ],
        'Api\V1\Rest\Login\Validator'          => [
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'username',
                'description'   => 'The Username or email address to use',
                'error_message' => 'Invalid Username',
            ],
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'password',
                'description'   => 'The Password',
                'error_message' => 'Invalid Password',
            ],
        ],
        'Api\V1\Rest\UserImage\Validator'      => [
            [
                'required'      => true,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'image_id',
                'description'   => 'Image Id from Cloudainiary',
                'error_message' => 'Invalid ImageId',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'url',
                'description' => 'Url for the image',
            ],
        ],
        'Api\V1\Rest\Import\Validator'         => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\File\UploadFile::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\File\RenameUpload::class,
                        'options' => [
                            'target'    => './tmp',
                            'randomize' => true,
                        ],
                    ],
                ],
                'name'        => 'file',
                'type'        => \Zend\InputFilter\FileInput::class,
                'description' => 'File to upload',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                'Nyc\DoeImporter',
                            ],
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'type',
                'description'   => 'the type of importer to use',
                'error_message' => 'Invalid import type',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'student_code',
                'description' => 'Student Access Code',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'teacher_code',
                'description' => 'Teacher Access Code',
            ],
            [
                'required'    => false,
                'allow_empty' => true,
                'validators'  => [
                    [
                        'name'    => \Application\Utils\Date\DateGreaterThanValidator::class,
                        'options' => [
                            'startDate' => 'now',
                        ],
                    ],
                ],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\ToNull::class,
                        'options' => [],
                    ],
                    [
                        'name'    => \Application\Utils\Date\ToDateFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'code_start',
                'description' => 'Start Date for Access Codes',
            ],
        ],
        'Api\V1\Rest\Password\Validator'       => [
            [
                'required'   => true,
                'validators' => [
                    [
                        'name'    => \Security\PasswordValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'    => [],
                'name'       => 'password',
            ],
        ],
        'Api\V1\Rest\Forgot\Validator'         => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'email',
                'description' => 'Email address or User Name of user to reset',
            ],
        ],
        'Api\V1\Rest\UserName\Validator'       => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \User\UserNameValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [],
                'name'        => 'user_name',
                'description' => 'The new Username selected',
            ],
        ],
        'Api\V1\Rest\FlipUser\Validator'       => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'flip_id',
                'description' => 'The Id of the flip the user has earned',
            ],
        ],
        'Api\V1\Rest\Friend\Validator'         => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Friend\AttachFriendValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [],
                'name'        => 'friend_id',
                'description' => 'The user Id of the person to friend',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'user_id',
                'description' => 'The user_id',
            ],
        ],
        'Api\V1\Rest\Suggest\Validator'        => [],
        'Api\V1\Rest\Reset\Validator'          => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\Regex::class,
                        'options' => [
                            'pattern' => '/^([a-zA-Z])[a-zA-Z0-9]{7,}$/',
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'code',
                'description' => 'The temporary code to use',
            ],
        ],
        'Api\V1\Rest\UpdatePassword\Validator' => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Security\PasswordValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'     => [],
                'name'        => 'password',
                'description' => 'New Password',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\Identical::class,
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'password_confirmation',
                'description' => 'Confirmed password',
            ],
        ],
        'Api\V1\Rest\SaveGame\Validator'       => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'data',
                'description' => 'The Data to save',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'version',
                'description' => 'The Version of the data',
            ],
        ],
        'Api\V1\Rest\Skribble\Validator'       => [
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                '1',
                            ],
                        ],
                    ],
                ],
                'filters'       => [],
                'name'          => 'version',
                'description'   => 'The version that was used to create this skribble',
                'error_message' => 'Invalid Version',
            ],
            [
                'required'      => true,
                'validators'    => [
                    [
                        'name'    => Skribble\Rule\RuleValidator::class,
                        'options' => [],
                    ],
                ],
                'filters'       => [],
                'name'          => 'rules',
                'description'   => 'The rules for creating the skribble',
                'error_message' => 'Invalid Rules',
            ],
            [
                'required'      => false,
                'validators'    => [],
                'filters'       => [],
                'name'          => 'friend_to',
                'description'   => 'The Friend to send this message to',
                'error_message' => 'Invalid Friend To',
            ],
            [
                'required'      => false,
                'validators'    => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                0,
                                1,
                            ],
                        ],
                    ],
                ],
                'filters'       => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => 'all'],
                    ],
                    [
                        'name'    => \Zend\Filter\ToInt::class,
                        'options' => [],
                    ],
                ],
                'name'          => 'read',
                'description'   => 'The Read flag',
                'error_message' => 'Invalid read flag',
            ],
        ],
        'Api\V1\Rest\SkribbleNotify\Validator' => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                'error',
                                'success',
                            ],
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'status',
                'description' => 'The status of the skribble',
            ],
        ],
        'Api\V1\Rest\Flag\Validator'           => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'flaggee',
                'description' => 'The person whose image is flagged',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'url',
                'description' => 'Url of the image flagged',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'reason',
                'description' => 'Reason for flagging',
            ],
        ],
        'Api\V1\Rest\Game\Validator'           => [
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'title',
                'description' => 'title of the game',
            ],
            [
                'required'    => true,
                'validators'  => [],
                'filters'     => [],
                'name'        => 'description',
                'description' => 'description of the game',
            ],
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\InArray::class,
                        'options' => [
                            'haystack' => [
                                0,
                                1,
                            ],
                        ],
                    ],
                ],
                'filters'     => [
                    [
                        'name'    => \Zend\Filter\Boolean::class,
                        'options' => ['type' => 'all'],
                    ],
                    [
                        'name'    => \Zend\Filter\ToInt::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'coming_soon',
                'description' => 'if the game is coming soon',
            ],
            [
                'required'    => false,
                'validators'  => [],
                'filters'     => [
                    [
                        'name'    => \Application\Utils\MetaFilter::class,
                        'options' => [],
                    ],
                ],
                'name'        => 'meta',
                'description' => 'meta data for game',
            ],
        ],
        'Api\V1\Rest\GroupReset\Validator'     => [
            [
                'required'    => true,
                'validators'  => [
                    [
                        'name'    => \Zend\Validator\Regex::class,
                        'options' => [
                            'pattern' => '/^([a-zA-Z])[a-zA-Z0-9]{7,}$/',
                        ],
                    ],
                ],
                'filters'     => [],
                'name'        => 'code',
                'description' => 'The temporary code to use',
            ],
        ],
    ],
];
