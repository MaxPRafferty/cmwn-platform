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
        $data     = [];
        $nameList = require getcwd() . '/config/autoload/names.global.php';

        foreach ($nameList['user-names']['left'] as $name) {
            array_push($data, ['name' => $name, 'position' => 'LEFT', 'count' => 1]);
        }

        foreach ($nameList['user-names']['right'] as $name) {
            array_push($data, ['name' => $name, 'position' => 'RIGHT', 'count' => 1]);
        }

        $table = $this->table('names');

        foreach ($data as $names) {
            $table->setData([]);
            try {
                $table
                    ->insert($names)
                    ->save();
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        'Got Exception When inserting name: ' . $exception->getMessage()
                    );
                }
            }

            $table->setData([]);
        }
    }
}
