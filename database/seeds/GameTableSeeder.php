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

            $games[] = User::create(array(
                'uuid' => 'polar-bear',
                'title' => 'Polar Bear',
                'description' => 'The magnificent Polar Bear is in danger of becoming extinct!  Find out all about where they live and why their fur looks white.  (Hint:  Things are not always as they appear!)  You get to play and do an offline experiment.'
            ));

            $games[] = User::create(array(
                'uuid' => 'sea-turtle',
                'title' => 'Sea Turtle',
                'description' => 'Can you ID the different kinds of animals?  Prove it or learn it right here!  Quick and fun, let your fingers do the clicking and be ready to show your stuff.'
            ));

            $games[] = User::create(array(
                'uuid' => 'animal-id',
                'title' => 'Animal ID',
                'description' => 'Sea Turtles are wondrous creatures!  Get cool turtle facts, play games and find out why they are endangered.'
            ));

            $this->command->info('Games Created!');
        }
    }
