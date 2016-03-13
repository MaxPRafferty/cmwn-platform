<?php // @codingStandardsIgnoreFile
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
$QUEUE = getenv('QUEUE');
if(empty($QUEUE)) {
    die("Set QUEUE env var containing the list of queues to work.\n");
}

chdir(dirname(__DIR__));

if (!file_exists('vendor/autoload.php')) {
    throw new RuntimeException(
        'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.'
    );
}

// Setup autoloading
include 'vendor/autoload.php';

if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
}

$appConfig = include APPLICATION_PATH . '/config/application.config.php';

// Some OS/Web Server combinations do not glob properly for paths unless they
// are fully qualified (e.g., IBM i). The following prefixes the default glob
// path with the value of the current working directory to ensure configuration
// globbing will work cross-platform.
if (isset($appConfig['module_listener_options']['config_glob_paths'])) {
    foreach ($appConfig['module_listener_options']['config_glob_paths'] as $index => $path) {
        if ($path !== 'config/autoload/{,*.}{global,local}.php') {
            continue;
        }
        $appConfig['module_listener_options']['config_glob_paths'][$index] = getcwd() . '/' . $path;
    }
}

// Run the application!
$app = Zend\Mvc\Application::init($appConfig);

// From bin/Rescue
$REDIS_BACKEND = getenv('REDIS_BACKEND');
if(!empty($REDIS_BACKEND)) {
    Resque::setBackend($REDIS_BACKEND);
}

$logLevel = 0;
$LOGGING = getenv('LOGGING');
$VERBOSE = getenv('VERBOSE');
$VVERBOSE = getenv('VVERBOSE');
if(!empty($LOGGING) || !empty($VERBOSE)) {
    $logLevel = Resque_Worker::LOG_NORMAL;
}
else if(!empty($VVERBOSE)) {
    $logLevel = Resque_Worker::LOG_VERBOSE;
}

$interval = 5;
$INTERVAL = getenv('INTERVAL');
if(!empty($INTERVAL)) {
    $interval = $INTERVAL;
}

$count = 1;
$COUNT = getenv('COUNT');
if(!empty($COUNT) && $COUNT > 1) {
    $count = $COUNT;
}

if($count > 1) {
    for($i = 0; $i < $count; ++$i) {
        $pid = pcntl_fork();
        if($pid == -1) {
            die("Could not fork worker ".$i."\n");
        }
        // Child, start the worker
        else if(!$pid) {
            $queues = explode(',', $QUEUE);
            $worker = $app->getServiceManager()->get('Job\Service\ResqueWorker');
            fwrite(STDOUT, '*** Starting worker '.$worker."\n");
            $worker->work($interval);
            break;
        }
    }
}
// Start a single worker
else {
    $queues = explode(',', $QUEUE);
    $worker = $app->getServiceManager()->get('Job\Service\ResqueWorker');

    $PIDFILE = getenv('PIDFILE');
    if ($PIDFILE) {
        file_put_contents($PIDFILE, getmypid()) or
        die('Could not write PID information to ' . $PIDFILE);
    }

    fwrite(STDOUT, '*** Starting worker '.$worker."\n");
    $worker->work($interval);
}