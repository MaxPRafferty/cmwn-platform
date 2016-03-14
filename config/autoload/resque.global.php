<?php
$cacheHost = getenv('CACHE1_HOST');
$cachePort = getenv('CACHE1_PORT');


$cacheHost = empty($cacheHost) ? 'localhost' : $cacheHost;
$cachePort = empty($cachePort) ? 6379 : $cachePort;

return [
    'resque' => [
        'backend' => $cacheHost . ':' . $cachePort,
    ],
];
