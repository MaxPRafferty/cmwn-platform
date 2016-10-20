<?php

$master = include __DIR__ . '/games.master.php';

$stagingGames = [];

$gameOnStaging = [
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
    'reef-builder',
];

foreach ($master['games']['master'] as $gameId => $gameData) {
    if (!in_array($gameId, $gameOnStaging)) {
        continue;
    }

    $comingSoon = in_array($gameId, $comingSoonGames) ? 1 : 0;

    $stagingData['coming_soon'] = $comingSoon;
    $stagingGames[$gameId]      = $gameData;
}

$master['games']['staging'] = $stagingGames;
return $master;
