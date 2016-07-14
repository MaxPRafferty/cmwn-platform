<?php

use Phinx\Seed\AbstractSeed;

/**
 * Class GameSeed
 *
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD)
 */
class GameSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $currentDate    = new \DateTime();
        $applicationEnv = getenv('APP_ENV') === false ? 'production' : getenv('APP_ENV');
        $gamesToAdd     = [];
        $gamesToRemove  = [];
        $gamesToEdit    = [];
        $gameList       = require __DIR__ . '/../../config/games/games.' . $applicationEnv . '.php';
        $gameList       = $gameList['games'][$applicationEnv];
        try {
            $existingStmt   = $this->query('SELECT * FROM games');
        } catch (\PDOException $exception) {
            if ($exception->getCode() != 23000) {
                $this->getOutput()->writeLn(
                    sprintf(
                        'Got Exception When trying to fetch game list: %s',
                        $exception->getMessage()
                    )
                );
            }
            throw $exception;
        }

        $currentGames   = [];
        $this->getOutput()->writeln('Im doing something');

        // Find all current games in the the DB
        foreach ($existingStmt as $key => $value) {
            $gameId = $value['game_id'];
            if (!isset($gameList[$gameId])) {
                $this->getOutput()->writeln(sprintf('The game "%s" is no longer in the list', $gameId));
                array_push($gamesToRemove, $gameId);
                continue;
            }

            $this->getOutput()->writeln('Im doing something 1');
            $currentGames[$gameId] = $value;
        }
        $this->getOutput()->writeln('Im doing something 2');
        // Check if the games have changed
        foreach ($currentGames as $gameId => $gameData) {
            $this->getOutput()->writeln('Im doing something 3');
            $gameConfig = $gameList[$gameId];
            $editGame   = false;

            if ($gameData['coming_soon'] != $gameConfig['coming_soon']) {
                $this->getOutput()->writeln(sprintf('The game "%s" has changed coming soon', $gameId));
                $gameData['coming_soon'] = $gameConfig['coming_soon'];
                $editGame                = true;
            }

            if ($gameData['title'] !== $gameConfig['title']) {
                $this->getOutput()->writeln(sprintf('The game "%s" has title', $gameId));
                $gameData['title'] = $gameConfig['title'];
                $editGame          = true;
            }

            if ($gameData['description'] !== $gameConfig['description']) {
                $this->getOutput()->writeln(sprintf('The game "%s" has description', $gameId));
                $gameData['description'] = $gameConfig['description'];
                $editGame                = true;
            }

            if ($editGame) {
                $gameData['updated'] = $currentDate->format('Y-m-d H:i:s');
                $gamesToEdit[$gameId] = $gameData;
            }
        }
        $this->getOutput()->writeln('Im doing something 4');

        // check for new games
        foreach ($gameList as $gameId => $gameData) {
            $this->getOutput()->writeln('Im doing something 5');
            if (isset($currentGames[$gameId])) {
                $this->getOutput()->writeln('Im doing something 6');
                // means we already have the game
                continue;
            }
            $this->getOutput()->writeln('Im doing something 7');

            $this->getOutput()->writeln(sprintf('New game found "%s"', $gameId));
            $gameData['created'] = $currentDate->format('Y-m-d H:i:s');
            $gameData['updated'] = $currentDate->format('Y-m-d H:i:s');
            array_push($gamesToAdd, $gameData);
        }

        $this->getOutput()->writeln('Im doing something 8');
        // remove games
        foreach ($gamesToRemove as $gameId) {
            try {
                $this->getOutput()->writeln(sprintf('Removing Game "%s"', $gameId));
                $this->query(sprintf(
                    "DELETE FROM games WHERE game_id='%s'",
                    $gameId
                ));
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        sprintf(
                            'Got Exception When trying to remove game "%s": %s',
                            $gameId,
                            $exception->getMessage()
                        )
                    );
                }
            }
        }

        // edit games
        foreach ($gamesToEdit as $gameId => $gameData) {
            try {
                $this->getOutput()->writeln(sprintf('Editing Game "%s"', $gameId));
                $this->query(sprintf(
                    "UPDATE games SET title = \"%s\", description = \"%s\", updated = '%s'  WHERE game_id='%s'",
                    $gameData['title'],
                    $gameData['description'],
                    $gameData['updated'],
                    $gameId
                ));
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        sprintf(
                            'Got Exception When trying to edit game "%s": %s',
                            $gameId,
                            $exception->getMessage()
                        )
                    );
                }
            }
        }

        $table = $this->table('games');
        // add games
        foreach ($gamesToAdd as $gameData) {
            try {
                $this->getOutput()->writeln(sprintf('Adding Game "%s"', $gameData['game_id']));
                $table->insert($gameData)
                ->saveData();
                $table->setData([]);
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        sprintf(
                            'Got Exception When trying to edit game "%s": %s',
                            $gameId,
                            $exception->getMessage()
                        )
                    );
                }
            }
        }
    }
}
