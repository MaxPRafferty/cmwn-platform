<?php

$awsKey    = getenv('AWS_KEY');
$awsSecret = getenv('AWS_SECRET');
$awsRegion = getenv('AWS_REGION');

$awsKey    = empty($awsKey) ? 'This is not a real key' : $awsKey;
$awsSecret = empty($awsSecret)
    ? 'This is not a real secret make an aws.local.php or set the correct Environment variables'
    : $awsSecret;

$awsRegion = empty($awsRegion) ? 'us-east-1' : $awsRegion;

return [
    'aws' => [
        'region'      => $awsRegion,
        'credentials' => [
            'key'    => $awsKey,
            'secret' => $awsSecret,
        ],
    ],
];
