<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

return [
    'router'          => [
        'routes' => [
            'home' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'invokables'         => [
            \Application\Listeners\ErrorListener::class          => \Application\Listeners\ErrorListener::class,
            \Application\Listeners\CacheExceptionListener::class =>
                \Application\Listeners\CacheExceptionListener::class,
        ],
        'initializers'       => [
            \Application\Service\LoggerAwareInitializer::class => \Application\Service\LoggerAwareInitializer::class,
        ],
        'factories'          => [
            \Application\Listeners\ListenersAggregate::class =>
                \Application\Listeners\ListenersAggregateFactory::class,
            \Application\Log\Rollbar\Options::class          => \Application\Log\Rollbar\OptionsFactory::class,
            'Application\Log\Rollbar\Notifier'               => \Application\Log\Rollbar\NotifierFactory::class,
            \Application\Log\Rollbar\Writer::class           => \Application\Log\Rollbar\WriterFactory::class,
            \Zend\Http\Client::class                         => \Application\Factory\HttpClientFactory::class,
        ],
        'abstract_factories' => [
            \Zend\Cache\Service\StorageCacheAbstractServiceFactory::class,
            \Zend\Db\Adapter\AdapterAbstractServiceFactory::class,
            \Application\Log\LoggerFactory::class,
            \Application\Utils\AbstractTableFactory::class,
        ],
    ],

    'shared-listeners' => [
        \Application\Listeners\ErrorListener::class,
        \Application\Listeners\CacheExceptionListener::class,
    ],

    'controllers' => [
        'aliases'    => [
            \Application\Controller\IndexController::class => '\Application\Controller\Index',
        ],
        'invokables' => [
            'Application\Controller\Index' => \Application\Controller\IndexController::class,
        ],
        'initializers'       => [
            \Application\Service\LoggerAwareInitializer::class => \Application\Service\LoggerAwareInitializer::class,
        ],
        'factories' => [
            'Application\Controller\Redis' => \Application\Controller\RedisControllerFactory::class,
        ]
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => [
            'layout/layout'           => realpath(__DIR__ . '/../view/layout/layout.phtml'),
            'application/index/index' => realpath(__DIR__ . '/../view/application/index/index.phtml'),
            'error/404'               => realpath(__DIR__ . '/../view/error/404.phtml'),
            'error/index'             => realpath(__DIR__ . '/../view/error/index.phtml'),
        ],
        'template_path_stack'      => [
            realpath(__DIR__ . '/../view'),
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'redis' => [
                    'options' => [
                        'route'    => 'redis:delete [--all] [--key=] [--verbose|-v] [--debug|-v]',
                        'defaults' => [
                            'controller' => 'Application\Controller\Redis',
                            'action'     => 'redisDelete',
                        ],
                    ],
                ],
            ]
        ],
    ],
];
