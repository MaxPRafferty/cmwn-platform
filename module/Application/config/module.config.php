<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

return [
    'validators' => [
        'factories' => [
            \Application\Utils\CheckIfDbRecordExists::class => \Application\Factory\CheckDbRecordFactory::class,
            \Application\Utils\CheckIfNoDbRecordExists::class => \Application\Factory\CheckDbRecordFactory::class,
        ]
    ],

    'rules' => [
        'factories' => [
            \Application\Rule\Session\Rule\HasValue::class            => \Rule\Rule\Service\BuildRuleFactory::class,
            \Application\Rule\Session\Rule\ValueEquals::class         => \Rule\Rule\Service\BuildRuleFactory::class,
            \Application\Rule\Session\Rule\ValueEqualsProvider::class => \Rule\Rule\Service\BuildRuleFactory::class,
        ],
        'shared'    => [
            \Application\Rule\Session\Rule\HasValue::class            => false,
            \Application\Rule\Session\Rule\ValueEquals::class         => false,
            \Application\Rule\Session\Rule\ValueEqualsProvider::class => false,
        ],
    ],

    'providers' => [
        'factories' => [
            \Application\Rule\Session\Provider\SessionContainer::class =>
                \Rule\Provider\Service\BuildProviderFactory::class,
            \Application\Rule\Session\Provider\SessionValue::class     =>
                \Rule\Provider\Service\BuildProviderFactory::class,
        ],
        'shared'    => [
            \Application\Rule\Session\Provider\SessionContainer::class => false,
            \Application\Rule\Session\Provider\SessionValue::class     => false,
        ],
    ],

    'actions'                                                         => [
        'factories' => [
            \Application\Rule\Session\Action\WriteProviderToSession::class =>
                \Rule\Action\Service\BuildActionFactory::class,
            \Application\Rule\Session\Action\WriteValueToSession::class    =>
                \Rule\Action\Service\BuildActionFactory::class,
        ],
        'shared'    => [
            \Application\Rule\Session\Action\WriteProviderToSession::class => false,
            \Application\Rule\Session\Action\WriteValueToSession::class    => false,
        ],
    ],

    \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class => [
        \Application\Session\CmwnContainer::class => [\Zend\Session\SessionManager::class],
    ],

    'service_manager' => [
        'initializers'       => [
            \Application\Service\LoggerAwareInitializer::class => \Application\Service\LoggerAwareInitializer::class,
        ],
        'factories'          => [
            \Application\Listeners\ErrorListener::class          =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Application\Listeners\CacheExceptionListener::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Application\Listeners\ListenersAggregate::class     =>
                \Application\Listeners\ListenersAggregateFactory::class,
            \Application\Log\Rollbar\Options::class              => \Application\Log\Rollbar\OptionsFactory::class,
            'Application\Log\Rollbar\Notifier'                   => \Application\Log\Rollbar\NotifierFactory::class,
            \Application\Log\Rollbar\Writer::class               => \Application\Log\Rollbar\WriterFactory::class,
            \Zend\Http\Client::class                             => \Application\Factory\HttpClientFactory::class,
            \Psr\Log\LoggerInterface::class                      => \Application\Log\PsrLoggerFactory::class,
        ],
        'abstract_factories' => [
            \Zend\Cache\Service\StorageCacheAbstractServiceFactory::class,
            \Zend\Db\Adapter\AdapterAbstractServiceFactory::class,
            \Application\Log\LoggerFactory::class,
            \Application\Service\AbstractTableFactory::class,
            \Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory::class,
        ],
    ],

    'shared-listeners' => [
        \Application\Listeners\ErrorListener::class,
        \Application\Listeners\CacheExceptionListener::class,
    ],

    'controllers' => [
        'initializers' => [
            \Application\Service\LoggerAwareInitializer::class => \Application\Service\LoggerAwareInitializer::class,
        ],
        'factories'    => [
            'Application\Controller\Redis' => \Application\Controller\RedisControllerFactory::class,
        ],
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
            ],
        ],
    ],
];
