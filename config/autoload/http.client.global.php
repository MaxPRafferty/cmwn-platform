<?php

return [
    'http-config' => [
        'timeout'            => 10,
        'storeresponse'      => false,
        'adapter'            => \Zend\Http\Client\Adapter\Socket::class,
        'ssltransport'       => 'tls',
        'sslverifypeer'      => true,
        'sslallowselfsigned' => false,
        'sslcert'            => 'data/ssl/cert.crt',
        'sslpassphrase'      => 'data/ssl/key.pem',
    ],
];
