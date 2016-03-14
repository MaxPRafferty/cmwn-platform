<?php

return [
    'service_manager' => [
        'aliases' => [
            'Nyc\StudentRegistry'
                => 'Import\Importer\Nyc\Students\StudentRegistry',

            'Nyc\TeacherRegistry'
                => 'Import\Importer\Nyc\Teachers\TeacherRegistry',

            'Nyc\ClassRegistry'
                => 'Import\Importer\Nyc\ClassRoom\ClassRoomRegistry',

            'Nyc\DoeImporter'
                => 'Import\Importer\Nyc\DoeImporter'
        ],

        'factories' => [
            'Import\Importer\Nyc\Students\StudentRegistry'
                => 'Import\Importer\Nyc\Students\StudentRegistryFactory',

            'Import\Importer\Nyc\Teachers\TeacherRegistry'
                => 'Import\Importer\Nyc\Teachers\TeacherRegistryFactory',

            'Import\Importer\Nyc\ClassRoom\ClassRoomRegistry'
                => 'Import\Importer\Nyc\ClassRoom\ClassRoomRegistryFactory',

            'Import\Importer\Nyc\Parser\DoeParser'
                => 'Import\Importer\Nyc\Parser\DoeParserFactory',

            'Import\Importer\Nyc\DoeImporter'
                => 'Import\Importer\Nyc\DoeImporterFactory'

        ],
    ],

    'controllers' => [
        'factories' => [
            'Import\Controller' => 'Import\Controller\ImportControllerFactory',
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'import-file' => [
                    'options' => [
                        'route'    => 'import:file <type> <file> <teacherCode> <studentCode>',
                        'defaults' => [
                            'controller' => 'Import\Controller',
                            'action'     => 'Import'
                        ],
                    ],
                ],
            ],
        ],
    ],
];
