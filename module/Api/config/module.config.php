<?php
return array(
    'shared-listeners' => array(
        0 => 'Api\\Listeners\\UserRouteListener',
        1 => 'Api\\Listeners\\UserGroupListener',
        2 => 'Api\\Listeners\\ImportRouteListener',
        3 => 'Api\\Listeners\\ScopeListener',
        4 => 'Api\\Listeners\\SuperMeListener',
        5 => 'Api\\Listeners\\UserImageListener',
        6 => 'Api\\Listeners\\ChangePasswordListener',
        7 => 'Api\\Listeners\\GroupRouteListener',
        8 => 'Api\\Listeners\\FriendListener',
        9 => 'Api\\Listeners\\UserHalLinksListener',
        10 => 'Api\\Listeners\\TemplateLinkListener',
        11 => 'Api\\Listeners\\GameRouteListener',
    ),
    'service_manager' => array(
        'invokables' => array(
            'Api\\Listeners\\ChangePasswordListener' => 'Api\\Listeners\\ChangePasswordListener',
            'Api\\Listeners\\TemplateLinkListener' => 'Api\\Listeners\\TemplateLinkListener',
        ),
        'factories' => array(
            'Api\\Listeners\\UserHalLinksListener' => 'Api\\Factory\\UserHalLinksListenerFactory',
            'Api\\Listeners\\ImportRouteListener' => 'Api\\Factory\\ImportRouteListenerFactory',
            'Api\\Listeners\\ScopeListener' => 'Api\\Factory\\ScopeListenerFactory',
            'Api\\Listeners\\UserRouteListener' => 'Api\\Factory\\UserRouteListenerFactory',
            'Api\\Listeners\\UserGroupListener' => 'Api\\Factory\\UserGroupListenerFactory',
            'Api\\Listeners\\GroupRouteListener' => 'Api\\Factory\\GroupRouteListenerFactory',
            'Api\\Listeners\\OrgRouteListener' => 'Api\\Factory\\OrgRouteListenerFactory',
            'Api\\Listeners\\SuperMeListener' => 'Api\\Factory\\SuperMeListenerFactory',
            'Api\\Listeners\\UserImageListener' => 'Api\\Factory\\UserImageListenerFactory',
            'Api\\V1\\Rest\\User\\UserResource' => 'Api\\V1\\Rest\\User\\UserResourceFactory',
            'Api\\V1\\Rest\\Org\\OrgResource' => 'Api\\V1\\Rest\\Org\\OrgResourceFactory',
            'Api\\V1\\Rest\\Game\\GameResource' => 'Api\\V1\\Rest\\Game\\GameResourceFactory',
            'Api\\V1\\Rest\\Image\\ImageResource' => 'Api\\V1\\Rest\\Image\\ImageResourceFactory',
            'Api\\V1\\Rest\\Group\\GroupResource' => 'Api\\V1\\Rest\\Group\\GroupResourceFactory',
            'Api\\V1\\Rest\\Token\\TokenResource' => 'Api\\V1\\Rest\\Token\\TokenResourceFactory',
            'Api\\V1\\Rest\\Login\\LoginResource' => 'Api\\V1\\Rest\\Login\\LoginResourceFactory',
            'Api\\V1\\Rest\\Logout\\LogoutResource' => 'Api\\V1\\Rest\\Logout\\LogoutResourceFactory',
            'Api\\V1\\Rest\\Forgot\\ForgotResource' => 'Api\\V1\\Rest\\Forgot\\ForgotResourceFactory',
            'Api\\V1\\Rest\\Password\\PasswordResource' => 'Api\\V1\\Rest\\Password\\PasswordResourceFactory',
            'Api\\V1\\Rest\\GroupUsers\\GroupUsersResource' => 'Api\\V1\\Rest\\GroupUsers\\GroupUsersResourceFactory',
            'Api\\V1\\Rest\\OrgUsers\\OrgUsersResource' => 'Api\\V1\\Rest\\OrgUsers\\OrgUsersResourceFactory',
            'Api\\V1\\Rest\\UserImage\\UserImageResource' => 'Api\\V1\\Rest\\UserImage\\UserImageResourceFactory',
            'Api\\V1\\Rest\\Import\\ImportResource' => 'Api\\V1\\Rest\\Import\\ImportResourceFactory',
            'Api\\V1\\Rest\\UserName\\UserNameResource' => 'Api\\V1\\Rest\\UserName\\UserNameResourceFactory',
            'Api\\V1\\Rest\\Flip\\FlipResource' => 'Api\\V1\\Rest\\Flip\\FlipResourceFactory',
            'Api\\V1\\Rest\\FlipUser\\FlipUserResource' => 'Api\\V1\\Rest\\FlipUser\\FlipUserResourceFactory',
            'Api\\V1\\Rest\\Friend\\FriendResource' => 'Api\\V1\\Rest\\Friend\\FriendResourceFactory',
            'Api\\Listeners\\FriendListener' => 'Api\\Factory\\FriendListenerFactory',
            'Api\\V1\\Rest\\Suggest\\SuggestResource' => 'Api\\V1\\Rest\\Suggest\\SuggestResourceFactory',
            'Api\\V1\\Rest\\Reset\\ResetResource' => 'Api\\V1\\Rest\\Reset\\ResetResourceFactory',
            'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordResource' => 'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordResourceFactory',
            'Api\\V1\\Rest\\SaveGame\\SaveGameResource' => 'Api\\V1\\Rest\\SaveGame\\SaveGameResourceFactory',
            'Api\\Listeners\\GameRouteListener' => 'Api\\Factory\\GameRouteListenerFactory',
            'Api\\V1\\Rest\\Media\\MediaResource' => 'Api\\V1\\Rest\\Media\\MediaResourceFactory',
            'Api\\V1\\Rest\\Skribble\\SkribbleResource' => 'Api\\V1\\Rest\\Skribble\\SkribbleResourceFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'api.rest.user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user[/:user_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\User\\Controller',
                    ),
                ),
            ),
            'api.rest.org' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/org[/:org_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Org\\Controller',
                    ),
                ),
            ),
            'api.rest.game' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/game[/:game_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Game\\Controller',
                    ),
                ),
            ),
            'api.rest.image' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/image[/:image_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Image\\Controller',
                    ),
                ),
            ),
            'api.rest.group' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/group[/:group_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Group\\Controller',
                    ),
                ),
            ),
            'api.rest.token' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Token\\Controller',
                    ),
                ),
            ),
            'api.rest.login' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Login\\Controller',
                    ),
                ),
            ),
            'api.rest.logout' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Logout\\Controller',
                    ),
                ),
            ),
            'api.rest.forgot' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/forgot[/:forgot_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Forgot\\Controller',
                    ),
                ),
            ),
            'api.rest.password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/password',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Password\\Controller',
                    ),
                ),
            ),
            'api.rest.group-users' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/group/:group_id/users',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\GroupUsers\\Controller',
                    ),
                ),
            ),
            'api.rest.org-users' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/org/:org_id/users',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\OrgUsers\\Controller',
                    ),
                ),
            ),
            'api.rest.user-image' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/image',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\UserImage\\Controller',
                    ),
                ),
            ),
            'api.rest.import' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/group/:group_id/import',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Import\\Controller',
                    ),
                ),
            ),
            'api.rest.user-name' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user-name[/:user_name_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\UserName\\Controller',
                    ),
                ),
            ),
            'api.rest.flip' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/flip[/:flip_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Flip\\Controller',
                    ),
                ),
            ),
            'api.rest.flip-user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/flip[/:flip_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\FlipUser\\Controller',
                    ),
                ),
            ),
            'api.rest.friend' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/friend[/:friend_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Friend\\Controller',
                    ),
                ),
            ),
            'api.rest.suggest' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/suggest[/:suggest_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Suggest\\Controller',
                    ),
                ),
            ),
            'api.rest.reset' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/reset[/:reset_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Reset\\Controller',
                    ),
                ),
            ),
            'api.rest.update-password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/password[/:update_password_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\UpdatePassword\\Controller',
                    ),
                ),
            ),
            'api.rest.save-game' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/game/:game_id',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\SaveGame\\Controller',
                    ),
                ),
            ),
            'api.rest.media' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/media[/:media_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Media\\Controller',
                    ),
                ),
            ),
            'api.rest.skribble' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:user_id/skribble[/:skribble_id]',
                    'defaults' => array(
                        'controller' => 'Api\\V1\\Rest\\Skribble\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'api.rest.user',
            1 => 'api.rest.org',
            2 => 'api.rest.game',
            3 => 'api.rest.image',
            4 => 'api.rest.group',
            5 => 'api.rest.token',
            6 => 'api.rest.login',
            7 => 'api.rest.logout',
            8 => 'api.rest.forgot',
            9 => 'api.rest.password',
            10 => 'api.rest.group-users',
            11 => 'api.rest.org-users',
            12 => 'api.rest.user-image',
            13 => 'api.rest.import',
            14 => 'api.rest.user-name',
            15 => 'api.rest.flip',
            16 => 'api.rest.flip-user',
            17 => 'api.rest.friend',
            18 => 'api.rest.suggest',
            19 => 'api.rest.reset',
            20 => 'api.rest.update-password',
            21 => 'api.rest.save-game',
            22 => 'api.rest.media',
            23 => 'api.rest.skribble',
        ),
    ),
    'zf-rest' => array(
        'Api\\V1\\Rest\\User\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\User\\UserResource',
            'route_name' => 'api.rest.user',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'user',
            'entity_http_methods' => array(
                0 => 'GET',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'type',
                1 => 'page',
                2 => 'per_page',
                3 => 'deleted',
                4 => 'username',
            ),
            'page_size' => 100,
            'page_size_param' => 'per_page',
            'entity_class' => 'Api\\V1\\Rest\\User\\UserEntity',
            'collection_class' => 'Api\\V1\\Rest\\User\\UserCollection',
            'service_name' => 'User',
        ),
        'Api\\V1\\Rest\\Org\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Org\\OrgResource',
            'route_name' => 'api.rest.org',
            'route_identifier_name' => 'org_id',
            'collection_name' => 'org',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PUT',
                2 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'page',
                1 => 'per_page',
                2 => 'type',
            ),
            'page_size' => 100,
            'page_size_param' => 'per_page',
            'entity_class' => 'Api\\V1\\Rest\\Org\\OrgEntity',
            'collection_class' => 'Api\\V1\\Rest\\Org\\OrgCollection',
            'service_name' => 'Org',
        ),
        'Api\\V1\\Rest\\Game\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Game\\GameResource',
            'route_name' => 'api.rest.game',
            'route_identifier_name' => 'game_id',
            'collection_name' => 'game',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(
                0 => 'page',
                1 => 'per_page',
            ),
            'page_size' => 100,
            'page_size_param' => 'per_page',
            'entity_class' => 'Api\\V1\\Rest\\Game\\GameEntity',
            'collection_class' => 'Api\\V1\\Rest\\Game\\GameCollection',
            'service_name' => 'Game',
        ),
        'Api\\V1\\Rest\\Image\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Image\\ImageResource',
            'route_name' => 'api.rest.image',
            'route_identifier_name' => 'image_id',
            'collection_name' => 'image',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => 'page',
            'entity_class' => 'Api\\V1\\Rest\\Image\\ImageEntity',
            'collection_class' => 'Api\\V1\\Rest\\Image\\ImageCollection',
            'service_name' => 'Image',
        ),
        'Api\\V1\\Rest\\Group\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Group\\GroupResource',
            'route_name' => 'api.rest.group',
            'route_identifier_name' => 'group_id',
            'collection_name' => 'group',
            'entity_http_methods' => array(
                0 => 'GET',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'page',
                1 => 'per_page',
                2 => 'type',
                3 => 'parent',
                4 => 'org_id',
            ),
            'page_size' => 100,
            'page_size_param' => 'per_page',
            'entity_class' => 'Api\\V1\\Rest\\Group\\GroupEntity',
            'collection_class' => 'Api\\V1\\Rest\\Group\\GroupCollection',
            'service_name' => 'Group',
        ),
        'Api\\V1\\Rest\\Token\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Token\\TokenResource',
            'route_name' => 'api.rest.token',
            'route_identifier_name' => 'token_id',
            'collection_name' => 'token',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Token\\TokenEntity',
            'collection_class' => 'Api\\V1\\Rest\\Token\\TokenCollection',
            'service_name' => 'Token',
        ),
        'Api\\V1\\Rest\\Login\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Login\\LoginResource',
            'route_name' => 'api.rest.login',
            'route_identifier_name' => 'login_id',
            'collection_name' => 'login',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Login\\LoginEntity',
            'collection_class' => 'Api\\V1\\Rest\\Login\\LoginCollection',
            'service_name' => 'login',
        ),
        'Api\\V1\\Rest\\Logout\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Logout\\LogoutResource',
            'route_name' => 'api.rest.logout',
            'route_identifier_name' => 'logout_id',
            'collection_name' => 'logout',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Logout\\LogoutEntity',
            'collection_class' => 'Api\\V1\\Rest\\Logout\\LogoutCollection',
            'service_name' => 'Logout',
        ),
        'Api\\V1\\Rest\\Forgot\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Forgot\\ForgotResource',
            'route_name' => 'api.rest.forgot',
            'route_identifier_name' => 'forgot_id',
            'collection_name' => 'forgot',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Forgot\\ForgotEntity',
            'collection_class' => 'Api\\V1\\Rest\\Forgot\\ForgotCollection',
            'service_name' => 'Forgot',
        ),
        'Api\\V1\\Rest\\Password\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Password\\PasswordResource',
            'route_name' => 'api.rest.password',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'password',
            'entity_http_methods' => array(
                1 => 'POST',
            ),
            'collection_http_methods' => array(
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Password\\PasswordEntity',
            'collection_class' => 'Api\\V1\\Rest\\Password\\PasswordCollection',
            'service_name' => 'Password',
        ),
        'Api\\V1\\Rest\\GroupUsers\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\GroupUsers\\GroupUsersResource',
            'route_name' => 'api.rest.group-users',
            'route_identifier_name' => 'group_id',
            'collection_name' => 'group_users',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\GroupUsers\\GroupUsersEntity',
            'collection_class' => 'Api\\V1\\Rest\\GroupUsers\\GroupUsersCollection',
            'service_name' => 'GroupUsers',
        ),
        'Api\\V1\\Rest\\OrgUsers\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\OrgUsers\\OrgUsersResource',
            'route_name' => 'api.rest.org-users',
            'route_identifier_name' => 'org_id',
            'collection_name' => 'org_users',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\OrgUsers\\OrgUsersEntity',
            'collection_class' => 'Api\\V1\\Rest\\OrgUsers\\OrgUsersCollection',
            'service_name' => 'OrgUsers',
        ),
        'Api\\V1\\Rest\\UserImage\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\UserImage\\UserImageResource',
            'route_name' => 'api.rest.user-image',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'user_image',
            'entity_http_methods' => array(
                0 => 'POST',
                1 => 'GET',
            ),
            'collection_http_methods' => array(),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\UserImage\\UserImageEntity',
            'collection_class' => 'Api\\V1\\Rest\\UserImage\\UserImageCollection',
            'service_name' => 'UserImage',
        ),
        'Api\\V1\\Rest\\Import\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Import\\ImportResource',
            'route_name' => 'api.rest.import',
            'route_identifier_name' => 'job_id',
            'collection_name' => 'import',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 100,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Import\\ImportEntity',
            'collection_class' => 'Api\\V1\\Rest\\Import\\ImportCollection',
            'service_name' => 'Import',
        ),
        'Api\\V1\\Rest\\UserName\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\UserName\\UserNameResource',
            'route_name' => 'api.rest.user-name',
            'route_identifier_name' => 'user_name_id',
            'collection_name' => 'user_name',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\UserName\\UserNameEntity',
            'collection_class' => 'Api\\V1\\Rest\\UserName\\UserNameCollection',
            'service_name' => 'UserName',
        ),
        'Api\\V1\\Rest\\Flip\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Flip\\FlipResource',
            'route_name' => 'api.rest.flip',
            'route_identifier_name' => 'flip_id',
            'collection_name' => 'flip',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '100',
            'page_size_param' => 'page',
            'entity_class' => 'Api\\V1\\Rest\\Flip\\FlipEntity',
            'collection_class' => 'Api\\V1\\Rest\\Flip\\FlipCollection',
            'service_name' => 'Flip',
        ),
        'Api\\V1\\Rest\\FlipUser\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\FlipUser\\FlipUserResource',
            'route_name' => 'api.rest.flip-user',
            'route_identifier_name' => 'flip_id',
            'collection_name' => 'flip_user',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '100',
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\FlipUser\\FlipUserEntity',
            'collection_class' => 'Api\\V1\\Rest\\FlipUser\\FlipUserCollection',
            'service_name' => 'FlipUser',
        ),
        'Api\\V1\\Rest\\Friend\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Friend\\FriendResource',
            'route_name' => 'api.rest.friend',
            'route_identifier_name' => 'friend_id',
            'collection_name' => 'friend',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Friend\\FriendEntity',
            'collection_class' => 'Api\\V1\\Rest\\Friend\\FriendCollection',
            'service_name' => 'friend',
        ),
        'Api\\V1\\Rest\\Suggest\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Suggest\\SuggestResource',
            'route_name' => 'api.rest.suggest',
            'route_identifier_name' => 'suggest_id',
            'collection_name' => 'suggest',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => '100',
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Suggest\\SuggestEntity',
            'collection_class' => 'Api\\V1\\Rest\\Suggest\\SuggestCollection',
            'service_name' => 'Suggest',
        ),
        'Api\\V1\\Rest\\Reset\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Reset\\ResetResource',
            'route_name' => 'api.rest.reset',
            'route_identifier_name' => 'reset_id',
            'collection_name' => 'reset',
            'entity_http_methods' => array(),
            'collection_http_methods' => array(
                0 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Reset\\ResetEntity',
            'collection_class' => 'Api\\V1\\Rest\\Reset\\ResetCollection',
            'service_name' => 'Reset',
        ),
        'Api\\V1\\Rest\\UpdatePassword\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordResource',
            'route_name' => 'api.rest.update-password',
            'route_identifier_name' => 'update_password_id',
            'collection_name' => 'update_password',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordEntity',
            'collection_class' => 'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordCollection',
            'service_name' => 'UpdatePassword',
        ),
        'Api\\V1\\Rest\\SaveGame\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\SaveGame\\SaveGameResource',
            'route_name' => 'api.rest.save-game',
            'route_identifier_name' => 'game_id',
            'collection_name' => 'save_game',
            'entity_http_methods' => array(
                0 => 'DELETE',
                1 => 'GET',
                2 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\SaveGame\\SaveGameEntity',
            'collection_class' => 'Api\\V1\\Rest\\SaveGame\\SaveGameCollection',
            'service_name' => 'SaveGame',
            'collection_http_methods' => array(),
        ),
        'Api\\V1\\Rest\\Media\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Media\\MediaResource',
            'route_name' => 'api.rest.media',
            'route_identifier_name' => 'media_id',
            'collection_name' => 'media',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Media\\MediaEntity',
            'collection_class' => 'Api\\V1\\Rest\\Media\\MediaCollection',
            'service_name' => 'Media',
        ),
        'Api\\V1\\Rest\\Skribble\\Controller' => array(
            'listener' => 'Api\\V1\\Rest\\Skribble\\SkribbleResource',
            'route_name' => 'api.rest.skribble',
            'route_identifier_name' => 'skribble_id',
            'collection_name' => 'skribble',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'page',
                1 => 'per_page',
                2 => 'status',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'Api\\V1\\Rest\\Skribble\\SkribbleEntity',
            'collection_class' => 'Api\\V1\\Rest\\Skribble\\SkribbleCollection',
            'service_name' => 'Skribble',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'Api\\V1\\Rest\\User\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Org\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Game\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Image\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Group\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Token\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Login\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Logout\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Forgot\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Password\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\GroupUsers\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\OrgUsers\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\UserImage\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Import\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\UserName\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Flip\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\FlipUser\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Friend\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Suggest\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Reset\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\UpdatePassword\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\SaveGame\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Media\\Controller' => 'HalJson',
            'Api\\V1\\Rest\\Skribble\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'Api\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Org\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Game\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Image\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Group\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Token\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Login\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Logout\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Forgot\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Password\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\GroupUsers\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\OrgUsers\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\UserImage\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Import\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\UserName\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Flip\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\FlipUser\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Friend\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Suggest\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Reset\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\UpdatePassword\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\SaveGame\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Media\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'Api\\V1\\Rest\\Skribble\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'Api\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Org\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Game\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Image\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Group\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Token\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Login\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Logout\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Forgot\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Password\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\GroupUsers\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\OrgUsers\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\UserImage\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Import\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
                2 => 'multipart/form-data',
            ),
            'Api\\V1\\Rest\\UserName\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Flip\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\FlipUser\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Friend\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Suggest\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Reset\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\UpdatePassword\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\SaveGame\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Media\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
            'Api\\V1\\Rest\\Skribble\\Controller' => array(
                0 => 'application/vnd.api.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'Api\\V1\\Rest\\User\\UserEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user',
                'route_identifier_name' => 'user_id',
                'max_depth' => 2,
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\User\\MeEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user',
                'route_identifier_name' => 'user_id',
                'max_depth' => 2,
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Org\\OrgEntity' => array(
                'entity_identifier_name' => 'org_id',
                'route_name' => 'api.rest.org',
                'route_identifier_name' => 'org_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Org\\OrgCollection' => array(
                'entity_identifier_name' => 'org_id',
                'route_name' => 'api.rest.org',
                'route_identifier_name' => 'org_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Game\\GameEntity' => array(
                'entity_identifier_name' => 'game_id',
                'route_name' => 'api.rest.game',
                'route_identifier_name' => 'game_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Game\\GameCollection' => array(
                'entity_identifier_name' => 'game_id',
                'route_name' => 'api.rest.game',
                'route_identifier_name' => 'game_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Image\\ImageEntity' => array(
                'entity_identifier_name' => 'image_id',
                'route_name' => 'api.rest.image',
                'route_identifier_name' => 'image_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Image\\ImageCollection' => array(
                'entity_identifier_name' => 'image_id',
                'route_name' => 'api.rest.image',
                'route_identifier_name' => 'image_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Group\\GroupEntity' => array(
                'entity_identifier_name' => 'group_id',
                'route_name' => 'api.rest.group',
                'route_identifier_name' => 'group_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Group\\GroupCollection' => array(
                'entity_identifier_name' => 'group_id',
                'route_name' => 'api.rest.group',
                'route_identifier_name' => 'group_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Token\\TokenEntity' => array(
                'entity_identifier_name' => 'token',
                'route_name' => 'api.rest.token',
                'route_identifier_name' => 'token_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Login\\LoginEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.login',
                'route_identifier_name' => 'login_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Login\\LoginCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.login',
                'route_identifier_name' => 'login_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Logout\\LogoutEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.logout',
                'route_identifier_name' => 'logout_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Logout\\LogoutCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.logout',
                'route_identifier_name' => 'logout_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Forgot\\ForgotEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.forgot',
                'route_identifier_name' => 'forgot_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Forgot\\ForgotCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.forgot',
                'route_identifier_name' => 'forgot_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Password\\PasswordEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.password',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Password\\PasswordCollection' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.password',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\GroupUsers\\GroupUsersEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.group-users',
                'route_identifier_name' => 'group_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\GroupUsers\\GroupUsersCollection' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.group-users',
                'route_identifier_name' => 'group_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\OrgUsers\\OrgUsersEntity' => array(
                'entity_identifier_name' => 'org_id',
                'route_name' => 'api.rest.org-users',
                'route_identifier_name' => 'org_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\OrgUsers\\OrgUsersCollection' => array(
                'entity_identifier_name' => 'org_id',
                'route_name' => 'api.rest.org-users',
                'route_identifier_name' => 'org_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\UserImage\\UserImageEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user-image',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\UserImage\\UserImageCollection' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.user-image',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Import\\ImportEntity' => array(
                'entity_identifier_name' => 'job_id',
                'route_name' => 'api.rest.import',
                'route_identifier_name' => 'import_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Import\\ImportCollection' => array(
                'entity_identifier_name' => 'job_id',
                'route_name' => 'api.rest.import',
                'route_identifier_name' => 'import_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\UserName\\UserNameEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.user-name',
                'route_identifier_name' => 'user_name_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\UserName\\UserNameCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.user-name',
                'route_identifier_name' => 'user_name_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Flip\\FlipEntity' => array(
                'entity_identifier_name' => 'flip_id',
                'route_name' => 'api.rest.flip',
                'route_identifier_name' => 'flip_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Flip\\FlipCollection' => array(
                'entity_identifier_name' => 'flip_id',
                'route_name' => 'api.rest.flip',
                'route_identifier_name' => 'flip_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\FlipUser\\FlipUserEntity' => array(
                'entity_identifier_name' => 'flip_id',
                'route_name' => 'api.rest.flip-user',
                'route_identifier_name' => 'flip_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\FlipUser\\FlipUserCollection' => array(
                'entity_identifier_name' => 'flip_id',
                'route_name' => 'api.rest.flip-user',
                'route_identifier_name' => 'flip_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Friend\\FriendEntity' => array(
                'entity_identifier_name' => 'friend_id',
                'route_name' => 'api.rest.friend',
                'route_identifier_name' => 'friend_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Friend\\FriendCollection' => array(
                'entity_identifier_name' => 'friend_id',
                'route_name' => 'api.rest.friend',
                'route_identifier_name' => 'friend_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Suggest\\SuggestEntity' => array(
                'entity_identifier_name' => 'suggest_id',
                'route_name' => 'api.rest.suggest',
                'route_identifier_name' => 'suggest_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Suggest\\SuggestCollection' => array(
                'entity_identifier_name' => 'suggest_id',
                'route_name' => 'api.rest.suggest',
                'route_identifier_name' => 'suggest_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Reset\\ResetEntity' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.reset',
                'route_identifier_name' => 'reset_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Reset\\ResetCollection' => array(
                'entity_identifier_name' => 'user_id',
                'route_name' => 'api.rest.reset',
                'route_identifier_name' => 'reset_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.update-password',
                'route_identifier_name' => 'update_password_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\UpdatePassword\\UpdatePasswordCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'api.rest.update-password',
                'route_identifier_name' => 'update_password_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\SaveGame\\SaveGameEntity' => array(
                'entity_identifier_name' => 'game_id',
                'route_name' => 'api.rest.save-game',
                'route_identifier_name' => 'game_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\SaveGame\\SaveGameCollection' => array(
                'entity_identifier_name' => 'game_id',
                'route_name' => 'api.rest.save-game',
                'route_identifier_name' => 'game_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Media\\MediaEntity' => array(
                'entity_identifier_name' => 'media_id',
                'route_name' => 'api.rest.media',
                'route_identifier_name' => 'media_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Media\\MediaCollection' => array(
                'entity_identifier_name' => 'media_id',
                'route_name' => 'api.rest.media',
                'route_identifier_name' => 'media_id',
                'is_collection' => true,
            ),
            'Media\\MediaCollection' => array(
                'entity_identifier_name' => 'media_id',
                'route_name' => 'api.rest.media',
                'route_identifier_name' => 'media_id',
                'is_collection' => true,
            ),
            'Api\\V1\\Rest\\Skribble\\SkribbleEntity' => array(
                'entity_identifier_name' => 'skribble_id',
                'route_name' => 'api.rest.skribble',
                'route_identifier_name' => 'skribble_id',
                'hydrator' => 'Zend\\Hydrator\\ArraySerializable',
            ),
            'Api\\V1\\Rest\\Skribble\\SkribbleCollection' => array(
                'entity_identifier_name' => 'skribble_id',
                'route_name' => 'api.rest.skribble',
                'route_identifier_name' => 'skribble_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'Api\\V1\\Rest\\User\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\User\\Validator',
        ),
        'Api\\V1\\Rest\\Org\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Org\\Validator',
        ),
        'Api\\V1\\Rest\\Image\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Image\\Validator',
        ),
        'Api\\V1\\Rest\\Group\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Group\\Validator',
        ),
        'Api\\V1\\Rest\\Login\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Login\\Validator',
        ),
        'Api\\V1\\Rest\\UserImage\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\UserImage\\Validator',
        ),
        'Api\\V1\\Rest\\Import\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Import\\Validator',
        ),
        'Api\\V1\\Rest\\Password\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Password\\Validator',
        ),
        'Api\\V1\\Rest\\Forgot\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Forgot\\Validator',
        ),
        'Api\\V1\\Rest\\UserName\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\UserName\\Validator',
        ),
        'Api\\V1\\Rest\\FlipUser\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\FlipUser\\Validator',
        ),
        'Api\\V1\\Rest\\Friend\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Friend\\Validator',
        ),
        'Api\\V1\\Rest\\Reset\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Reset\\Validator',
        ),
        'Api\\V1\\Rest\\UpdatePassword\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\UpdatePassword\\Validator',
        ),
        'Api\\V1\\Rest\\SaveGame\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\SaveGame\\Validator',
        ),
        'Api\\V1\\Rest\\Skribble\\Controller' => array(
            'input_filter' => 'Api\\V1\\Rest\\Skribble\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'Api\\V1\\Rest\\User\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'first_name',
                'description' => 'Users First name',
                'error_message' => 'First name is invalid',
            ),
            1 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'middle_name',
                'description' => 'Users Middle Name',
                'error_message' => 'Middle name is invalid',
            ),
            2 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'last_name',
                'description' => 'Users Last name',
                'error_message' => 'Last name is invalid',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'gender',
                'description' => 'Users Gender',
                'error_message' => 'Invalid Gender',
            ),
            4 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(
                    0 => array(
                        'name' => 'Application\\Utils\\MetaFilter',
                        'options' => array(),
                    ),
                ),
                'name' => 'meta',
                'description' => 'meta data',
            ),
            5 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'User\\TypeValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'type',
                'description' => 'The type of user',
            ),
            6 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'username',
                'description' => 'Users name',
                'error_message' => 'Invalid Username',
            ),
            7 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\EmailAddress',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'email',
                'description' => 'Users Email',
                'error_message' => 'Invalid Email',
            ),
            8 => array(
                'required' => false,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\Date',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'birthdate',
                'description' => 'birthdate',
                'error_message' => 'Invalid Birthdate',
            ),
        ),
        'Api\\V1\\Rest\\Org\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'title',
                'description' => 'Organizations title',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'description',
                'description' => 'Organizations description',
            ),
            2 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'type',
                'description' => 'Type of organization',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(
                    0 => array(
                        'name' => 'Application\\Utils\\MetaFilter',
                        'options' => array(),
                    ),
                ),
                'name' => 'meta',
                'description' => 'Meta data for the organization',
            ),
        ),
        'Api\\V1\\Rest\\Image\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'public_id',
                'description' => 'The Image Id',
                'error_message' => 'Invalid Image Id',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\InArray',
                        'options' => array(
                            'haystack' => array(
                                0 => 'moderation',
                            ),
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'notification_type',
                'description' => 'Notification type',
                'error_message' => 'Invalid Notification Type',
            ),
            2 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\InArray',
                        'options' => array(
                            'haystack' => array(
                                0 => 'approved',
                                1 => 'rejected',
                                2 => 'pending',
                            ),
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'moderation_status',
                'description' => 'Moderation status',
                'error_message' => 'Invalid Moderation status',
            ),
        ),
        'Api\\V1\\Rest\\Group\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'organization_id',
                'description' => 'The Id of the organization this group belongs too',
                'error_message' => 'Invalid Organization or not found',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'title',
                'description' => 'The title for this Organization',
                'error_message' => 'Invalid Title',
            ),
            2 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'max' => '255',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'description',
                'description' => 'Organizations description',
                'error_message' => 'Invalid Description',
            ),
            3 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'meta',
                'description' => 'Meta data for the group',
                'error_message' => 'Invalid Meta data',
            ),
        ),
        'Api\\V1\\Rest\\Login\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'username',
                'description' => 'The Username or email address to use',
                'error_message' => 'Invalid Username',
            ),
            1 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'password',
                'description' => 'The Password',
                'error_message' => 'Invalid Password',
            ),
        ),
        'Api\\V1\\Rest\\UserImage\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'image_id',
                'description' => 'Image Id from Cloudainiary',
                'error_message' => 'Invalid ImageId',
            ),
            1 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'url',
                'description' => 'Url for the image',
            ),
        ),
        'Api\\V1\\Rest\\Import\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\File\\UploadFile',
                        'options' => array(),
                    ),
                ),
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\File\\RenameUpload',
                        'options' => array(
                            'target' => './tmp',
                            'randomize' => true,
                        ),
                    ),
                ),
                'name' => 'file',
                'type' => 'Zend\\InputFilter\\FileInput',
                'description' => 'File to upload',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\InArray',
                        'options' => array(
                            'haystack' => array(
                                0 => 'Nyc\\DoeImporter',
                            ),
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'type',
                'description' => 'the type of importer to use',
                'error_message' => 'Invalid import type',
            ),
            2 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'student_code',
                'description' => 'Student Access Code',
            ),
            3 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'teacher_code',
                'description' => 'Teacher Access Code',
            ),
        ),
        'Api\\V1\\Rest\\Password\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Security\\PasswordValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'password',
            ),
        ),
        'Api\\V1\\Rest\\Forgot\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'email',
                'description' => 'Email address or User Name of user to reset',
            ),
        ),
        'Api\\V1\\Rest\\UserName\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'User\\UserNameValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'user_name',
                'description' => 'The new Username selected',
            ),
        ),
        'Api\\V1\\Rest\\FlipUser\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'flip_id',
                'description' => 'The Id of the flip the user has earned',
            ),
        ),
        'Api\\V1\\Rest\\Friend\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Friend\\AttachFriendValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'friend_id',
                'description' => 'The user Id of the person to friend',
            ),
            1 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'user_id',
                'description' => 'The user_id',
            ),
        ),
        'Api\\V1\\Rest\\Reset\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\Regex',
                        'options' => array(
                            'pattern' => '/^([a-zA-Z])[a-zA-Z0-9]{7,}$/',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'code',
                'description' => 'The temporary code to use',
            ),
        ),
        'Api\\V1\\Rest\\UpdatePassword\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Security\\PasswordValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'password',
                'description' => 'New Password',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\Identical',
                        'options' => array(
                            'token' => 'password',
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'password_confirmation',
                'description' => 'Confirmed password',
            ),
        ),
        'Api\\V1\\Rest\\SaveGame\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'data',
                'description' => 'The Data to save',
            ),
            1 => array(
                'required' => true,
                'validators' => array(),
                'filters' => array(),
                'name' => 'version',
                'description' => 'The Version of the data',
            ),
        ),
        'Api\\V1\\Rest\\Skribble\\Validator' => array(
            0 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\InArray',
                        'options' => array(
                            'haystack' => array(
                                0 => '1',
                            ),
                        ),
                    ),
                ),
                'filters' => array(),
                'name' => 'version',
                'description' => 'The version that was used to create this skribble',
                'error_message' => 'Invalid Version',
            ),
            1 => array(
                'required' => true,
                'validators' => array(
                    0 => array(
                        'name' => 'Skribble\\Rule\\RuleValidator',
                        'options' => array(),
                    ),
                ),
                'filters' => array(),
                'name' => 'rules',
                'description' => 'The rules for creating the skribble',
                'error_message' => 'Invalid Rules',
            ),
            2 => array(
                'required' => false,
                'validators' => array(),
                'filters' => array(),
                'name' => 'friend_to',
                'description' => 'The Friend to send this message to',
                'error_message' => 'Invalid Friend To',
            ),
        ),
    ),
);
