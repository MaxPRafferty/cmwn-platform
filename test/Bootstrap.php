<?php

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

// Include Composer autoload
include '../vendor/autoload.php';

// Find all test namespaces
$directoryIterator = new \DirectoryIterator('../module');
$nameSpaces        = [];

foreach ($directoryIterator as $directory) {
    if ($directory->isDot()) {
        continue;
    }

    // Add Source files
    $nameSpaces[$directory->getFilename()] = $directory->getPathname() . '/src/' . $directory->getFilename();

    // Add test Files
    $nameSpaces[$directory->getFilename() . 'Test'] =
        $directory->getPathname() . '/test/' . $directory->getFilename() . 'Test';
}

// Include Integration test
$nameSpaces['IntegrationTest'] = __DIR__ . '/IntegrationTest';
\Zend\Loader\AutoloaderFactory::factory([
    'Zend\Loader\StandardAutoloader' => [
        'namespaces' => $nameSpaces
    ]
]);

// Bootstrap the application
\IntegrationTest\TestHelper::getServiceManager();
unset($settings, $nameSpaces, $directory, $directoryIterator);