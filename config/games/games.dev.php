<?php

$master = include __DIR__ . '/games.master.php';

// make all games open instead

$devGames = [];

foreach ($master['games']['master'] as $gameId => $gameData) {
    $gameData['coming_soon'] = 0;
    $devGames[$gameId] = $gameData;
}

$master['games']['dev'] = $devGames;
return $master;
