<?php

use Illuminate\Database\Seeder;
use app\Game;

class GameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        DB::table('games')->delete();

        $this->command->info('Creating Games!');

        $games[] = Game::create(array(
            'uuid' => 'polar-bear',
            'title' => 'Polar Bear',
            'description' => 'Description: Polar Bear description goes here.',
        ));

        $games[] = Game::create(array(
            'uuid' => 'sea-turtle',
            'title' => 'Sea Turtle',
            'description' => 'Description: Sea Turtle description goes here.',
        ));

        $games[] = Game::create(array(
            'uuid' => 'animal-id',
            'title' => 'Animal ID',
            'description' => 'Description: Animal ID description goes here.',
        ));

        $this->command->info('Games Created!');
    }
}
