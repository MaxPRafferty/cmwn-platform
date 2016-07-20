<?php

$master = include __DIR__ . '/games.master.php';

// make all games open instead

$qaGames = [];

foreach ($master['games']['master'] as $gameId => $gameData) {
    $gameData['coming_soon'] = 0;
    $qaGames[$gameId]        = $gameData;
}

$master['games']['qa'] = $qaGames;
return $master;
