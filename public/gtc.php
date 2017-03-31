<?php // @codingStandardsIgnoreFile
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
include 'vendor/autoload.php';

if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
}

$appConfig = include APPLICATION_PATH . '/config/application.config.php';

if (file_exists(APPLICATION_PATH . '/config/development.config.php')) {
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig, include APPLICATION_PATH . '/config/development.config.php');
}

// Run the application!
$app = ZF\Apigility\Application::init($appConfig);

/** @var \Zend\Db\Adapter\Adapter $adapter */
$adapter = $app->getServiceManager()->get(\Zend\Db\Adapter\Adapter::class);
/** @var \Zend\Db\TableGateway\TableGateway $flipsGateway */
$flipsGateway = $app->getServiceManager()->get('Table/user_flips');
/** @var \Zend\Db\TableGateway\TableGateway $gamesGateway */
$gamesGateway = $app->getServiceManager()->get('Table/user_games');
$sql     = new Zend\Db\Sql\Sql($adapter);

// The flips in question ( in order that they are earned)
$gtcFlips = [
    0 => 'recycling-champion',
    1 => 'priceless-pourer',
    2 => 'fantastic-food-sharer',
    3 => 'dynamic-diverter',
    4 => 'master-sorter',
    5 => 'green-team-challenge',
];

// list of the GTC games (order does not matter)
$gtcGames = [
    'gtc-recycling-champion',
    'gtc-priceless-pourer',
    'gtc-dynamic-diverter',
    'gtc-fantastic-food-sharer',
    'gtc-master-sorter',
];

$gtcPath = [
    // Earned Flip             // Unlocks Game
    'recycling-champion'    => 'gtc-priceless-pourer',
    'priceless-pourer'      => 'gtc-fantastic-food-sharer',
    'fantastic-food-sharer' => 'gtc-dynamic-diverter',
    'dynamic-diverter'      => 'gtc-master-sorter',
];

// Holds a list of the flips a user has earned and the games they can play
$userInfo = [];

// find all the users that have the flips in question and group together
$select = new \Zend\Db\Sql\Select(['uf' => 'user_flips']);
$where  = new \Zend\Db\Sql\Where();

$where->addPredicate(new \Zend\Db\Sql\Predicate\In('flip_id', $gtcFlips));
$select->where($where);
$select->group(['user_id', 'flip_id']);
$select->order('user_id');
$stmt = $sql->buildSqlString($select);

$results = $adapter->query($stmt, $adapter::QUERY_MODE_EXECUTE);

foreach ($results as $userData) {
    $userId = $userData['user_id'];
    $flipId = $userData['flip_id'];
    $earned = $userData['earned'];
    if (!isset($userInfo[$userId])) {
        $userInfo[$userId] = ['flips' => [], 'games' => []];
    }

    $userInfo[$userId]['flips'][$flipId] = $earned;
}

// Find all the games for the users
$select = new \Zend\Db\Sql\Select(['ug' => 'user_games']);
$where  = new \Zend\Db\Sql\Where();

$where->addPredicate(new \Zend\Db\Sql\Predicate\In('game_id', $gtcGames));
$select->where($where);
$select->group(['user_id', 'game_id']);
$select->order('user_id');
$stmt = $sql->buildSqlString($select);

$results = $adapter->query($stmt, $adapter::QUERY_MODE_EXECUTE);

foreach ($results as $userData) {
    $userId = $userData['user_id'];
    $gameId = $userData['game_id'];
    if (!isset($userInfo[$userId])) {
        $userInfo[$userId] = ['flips' => [], 'games' => []];
    }

    $userInfo[$userId]['games'][$gameId] = $gameId;
}

$flipsBatch = []; // flips to reward for user
$gamesBatch = []; // games to unlock for user

echo 'Checking Flips' . PHP_EOL;

// Check that the user has all the flips needed and award missing
foreach ($userInfo as $userId => $info) {
    $flipInfo = $info['flips'];
    $lastFlipFound = false;
    $lastEarned = null;
    foreach (array_reverse($gtcFlips) as $order => $flipId) {
        if (!$lastFlipFound && isset($flipInfo[$flipId])) {
        //    echo sprintf('Last flip earned for %s is %s' . PHP_EOL, $userId, $flipId);
            $lastFlipFound = true;
            $lastEarned = $flipInfo[$flipId];
        }

        if ($lastFlipFound && !isset($flipInfo[$flipId])) {
            echo sprintf('User %s should have earned %s' . PHP_EOL, $userId, $flipId);
            $flipsBatch[] = ['user_id' => $userId, 'flip_id' => $flipId, 'earned' => $lastEarned];
            $userInfo[$userId]['flips'][$flipId] = $lastEarned;
        }
    }
}

echo PHP_EOL;
echo 'Checking Games' . PHP_EOL;
// Check the user has access to the games and allow them to play if they can
foreach ($userInfo as $userId => $info) {
    $gameInfo = $info['games'];
    $flipInfo = $info['flips'];
    foreach ($flipInfo as $flipId => $earned) {
        $expectedGame = $gtcPath[$flipId] ?? null;
        if (is_null($expectedGame)) {
            continue;
        }

        if (!isset($gameInfo[$expectedGame])) {
            echo sprintf('User %s should be able to play %s' . PHP_EOL, $userId, $expectedGame);
            $gamesBatch[] = ['user_id' => $userId, 'game_id' => $expectedGame];
        }
    }
}


// lets reward the flips
echo PHP_EOL;
echo 'Rewarding flips' . PHP_EOL;
foreach ($flipsBatch as $batchData) {
    try {
        echo sprintf('Rewarding %s for %s on %s' . PHP_EOL, $batchData['flip_id'], $batchData['user_id'], $batchData['earned']);
        $flipsGateway->insert($batchData);
    } catch (\Exception $exception) {
        var_dump($exception);
    }
}

// lets unlock the games
echo PHP_EOL;
echo 'Unlocking Games' . PHP_EOL;
foreach ($gamesBatch as $batchData) {
    try {
        echo sprintf('Unlocking %s for %s' . PHP_EOL, $batchData['game_id'], $batchData['user_id']);
        $gamesGateway->insert($batchData);
    } catch (\Exception $exception) {
        var_dump($exception);
    }
}

echo 'done';