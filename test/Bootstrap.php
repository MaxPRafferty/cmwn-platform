<?php

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

// TODO Remove this when all the tests are updated for ZF

class_alias(
    \PHPUnit\Framework\ExpectationFailedException::class,
    '\PHPUnit_Framework_ExpectationFailedException'
);

class_alias(
    \PHPUnit\Framework\TestCase::class,
    '\PHPUnit_Framework_TestCase'
);

@unlink(__DIR__ . '/../data/cache/module-classmap-cache.test.module.cache.php');
@unlink(__DIR__ . '/../data/cache/module-config-cache.test.config.cache.php');

include '../vendor/autoload.php';
