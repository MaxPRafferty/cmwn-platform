<?php

return [
    'job_runner' => [
        'allowed_jobs' => [
            'Import\Importer\Nyc\DoeImporter' => [
                'command' => 'import:file',
                'params'  => [
                    'type',
                    'file',
                    'teacher_code',
                    'student_code',
                    'school',
                    'email',
                ],
            ],
        ],
    ],

    'service_manager' => [
        'aliases' => [
            'Nyc\StudentRegistry' => \Import\Importer\Nyc\Students\StudentRegistry::class,
            'Nyc\TeacherRegistry' => \Import\Importer\Nyc\Teachers\TeacherRegistry::class,
            'Nyc\ClassRegistry'   => \Import\Importer\Nyc\ClassRoom\ClassRoomRegistry::class,
            'Nyc\DoeImporter'     => \Import\Importer\Nyc\DoeImporter::class,
        ],

        'factories' => [
            \Import\Importer\Nyc\Students\StudentRegistry::class    =>
                \Import\Importer\Nyc\Students\StudentRegistryFactory::class,
            \Import\Importer\Nyc\Teachers\TeacherRegistry::class    =>
                \Import\Importer\Nyc\Teachers\TeacherRegistryFactory::class,
            \Import\Importer\Nyc\ClassRoom\ClassRoomRegistry::class =>
                \Import\Importer\Nyc\ClassRoom\ClassRoomRegistryFactory::class,
            \Import\Importer\Nyc\Parser\DoeParser::class            =>
                \Import\Importer\Nyc\Parser\DoeParserFactory::class,
            \Import\Importer\Nyc\DoeImporter::class                 => \Import\Importer\Nyc\DoeImporterFactory::class,
        ],
    ],

    'controllers' => [
        'factories' => [
            'Import\Controller' => \Import\Controller\ImportControllerFactory::class,
        ],
    ],

    'console' => [
        'router' => [
            'routes' => [
                'import-file' => [
                    'options' => [
                        // @codingStandardsIgnoreStart
                        'route'    => 'import:file --type= --file= --teacherCode= --studentCode= --school= --email= [--verbose|-v] [--debug|-d] [--dry-run]',
                        // @codingStandardsIgnoreEnd
                        'defaults' => [
                            'controller' => 'Import\Controller',
                            'action'     => 'Import',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
