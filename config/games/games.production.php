<?php

$master = include __DIR__ . '/games.master.php';

// make all games open instead

$productionGames = [];

$gameOnProduction = [
    'animal-id',
    'be-bright',
    'bloom-boom',
    'carbon-catcher',
    'drought-out',
    'fire',
    'happy-fish-face',
    'litter-bug',
    'meerkat-mania',
    'monarch',
    'pedal-pusher',
    'polar-bear',
    'printmaster',
    'reef-builder',
    'salad-rain',
    'sea-turtle',
    'tag-it',
    'turtle-hurdle',
    'twirl-n-swirl',
    'twirling-tower',
    'waterdrop',
];

$comingSoonGames = [
    'fire',
    'monarch',
    'waterdrop',
    'bloom-boom',
    'polar-bear',
];

foreach ($master['games']['master'] as $gameId => $gameData) {
    if (in_array($gameId, $gameOnProduction)) {
        continue;
    }

    $comingSoon = in_array($gameId, $comingSoonGames) ? 1 : 0;

    $productionData['coming_soon'] = $comingSoon;
    $stagingGames[$gameId]         = $gameData;
}

$master['games']['production'] = $productionGames;
return $master;
