<?php

return [
    'service_manager' => [
        'aliases' => [
            'Nyc\StudentRegistry'
                => 'Import\Importer\Nyc\Students\StudentRegistry',
            'Nyc\TeacherRegistry'
                => 'Import\Importer\Nyc\Teachers\TeacherRegistry',
            'Nyc\ClassRoomRegistry'
                => 'Import\Importer\Nyc\ClassRooms\ClassRoomRegistry',

            'Nyc\DoeImporter'
                => 'Import\Importer\Nyc\Parser\DoeParser'
        ],
        'factories' => [
            'Import\Importer\Nyc\Students\StudentRegistry'
                => 'Import\Importer\Nyc\Students\StudentRegistryFactory',
            'Import\Importer\Nyc\Teachers\TeacherRegistry'
                => 'Import\Importer\Nyc\Teachers\TeacherRegistryFactory',
            'Import\Importer\Nyc\ClassRooms\ClassRoomRegistry'
                => 'Import\Importer\Nyc\ClassRooms\ClassRoomRegistryFactory',

            'Import\Importer\Nyc\Parser\DoeParser' => 'Import\Importer\Nyc\Parser\DoeParserFactory'
        ],
    ],
];
