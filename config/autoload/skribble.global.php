<?php

$skribbleSNSArn = getenv('SKRIBBLE_SNS_ARN');
$skribbleSNSArn = empty($skribbleSNSArn) ? 'not a real arn' : $skribbleSNSArn;

return [
    'skribble-sns-config' => [
        'sns-arn' => $skribbleSNSArn,
    ],
];
