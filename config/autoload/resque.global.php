<?php
$cacheHost = getenv('CACHE1_HOST');
$cachePort = getenv('CACHE1_PORT');

return [
    'resque' => [
        'backend' => $cacheHost . ':' . $cachePort,
    ],
];
