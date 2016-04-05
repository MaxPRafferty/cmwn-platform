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
];
