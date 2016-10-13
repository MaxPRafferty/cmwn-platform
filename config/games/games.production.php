<?php

$master = include __DIR__ . '/games.master.php';

// make all games open instead

$productionGames = [];

$gameOnProduction = [
    'all-about-you',
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
    'skribble',
];

$comingSoonGames = [
    'fire',
    'monarch',
    'sea-turtle',
    'reef-builder',
];

foreach ($master['games']['master'] as $gameId => $gameData) {
    if (!in_array($gameId, $gameOnProduction)) {
        continue;
    }

    $comingSoon = in_array($gameId, $comingSoonGames) ? 1 : 0;

    $productionData['coming_soon'] = $comingSoon;
    $productionGames[$gameId]      = $gameData;
}

$master['games']['production'] = $productionGames;
return $master;

