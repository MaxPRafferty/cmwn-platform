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
            'Application\Listeners\ErrorListener' => 'Application\Listeners\ErrorListener',
        ],
        'initializers'       => [
            'Application\Service\LoggerAwareInitializer' => 'Application\Service\LoggerAwareInitializer',

        ],
        'factories'          => [
            'Application\Listeners\ListenersAggregate' => 'Application\Listeners\ListenersAggregateFactory',
            'Application\Log\Rollbar\Options'          => 'Application\Log\Rollbar\OptionsFactory',
            'Application\Log\Rollbar\Notifier'         => 'Application\Log\Rollbar\NotifierFactory',
            'Application\Log\Rollbar\Writer'           => 'Application\Log\Rollbar\WriterFactory',
        ],
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
            'Application\Log\LoggerFactory',
            'Application\Utils\AbstractTableFactory',
        ],
    ],

    'shared-listeners' => [
        'Application\Listeners\ErrorListener',
    ],

    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => realpath(__DIR__ . '/../view/layout/layout.phtml'),
            'application/index/index' => realpath(__DIR__ . '/../view/application/index/index.phtml'),
            'error/404'               => realpath(__DIR__ . '/../view/error/404.phtml'),
            'error/index'             => realpath(__DIR__ . '/../view/error/index.phtml'),
        ],
        'template_path_stack' => [
            realpath(__DIR__ . '/../view'),
        ],
    ],
];
