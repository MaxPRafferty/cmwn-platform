<?php

use Phinx\Seed\AbstractSeed;

class NamesSeeds extends AbstractSeed
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
        $namesToAdd    = [];
        $namesToRemove = [];
        $nameList      = require __DIR__ . '/../../config/autoload/names.global.php';
        $existingStmt  = $this->query('SELECT * FROM names');
        $currentNames  = [];

        foreach ($existingStmt as $key => $value) {
            $name                = $value['name'];
            $currentNames[$name] = $name;
            $pos                 = strtolower($value['position']);
            if (array_search($value['name'], $nameList['user-names'][$pos]) === false) {
                $this->getOutput()->writeln(sprintf('The name "%s" is no longer in the list', $name));
                array_push($namesToRemove, ['name' => $name, 'position' => 'LEFT']);
            }
        }

        foreach ($nameList['user-names']['left'] as $name) {
            if (!array_key_exists($name, $currentNames)) {
                $this->getOutput()->writeln(sprintf('Adding "%s" to database', $name));
                array_push($namesToAdd, ['name' => $name, 'position' => 'LEFT', 'count' => 1]);
            }
        }

        foreach ($nameList['user-names']['right'] as $name) {
            if (!array_key_exists($name, $currentNames)) {
                $this->getOutput()->writeln(sprintf('Adding "%s" to database', $name));
                array_push($namesToAdd, ['name' => $name, 'position' => 'RIGHT', 'count' => 1]);
            }
        }

        $table = $this->table('names');

        foreach ($namesToAdd as $add) {
            $table->setData([]);
            try {
                $this->getOutput()->writeln(sprintf('Inserting name "%s"', $add['name']));
                $table
                    ->insert($add)
                    ->save();
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        'Got Exception When inserting name: ' . $exception->getMessage()
                    );
                }
            }
        }

        foreach ($namesToRemove as $remove) {
            $table->setData([]);
            try {
                $this->getOutput()->writeln(sprintf('Deleting name "%s"', $remove['name']));
                $this->query(sprintf(
                    "DELETE FROM names WHERE name='%s' AND position = '%s'",
                    $remove['name'],
                    $remove['position']
                ));
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        'Got Exception When inserting name: ' . $exception->getMessage()
                    );
                }
            }
        }
    }
}
