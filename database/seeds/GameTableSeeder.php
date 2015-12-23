<?php

    use Illuminate\Database\Seeder;
    use app\Game;

    class GameTableSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            $faker = Faker\Factory::create();
            DB::table('games')->delete();

            $games =   "air-transformer, animal-id,be-bright,bike-bits,can-it,cloud-in-a-jar,drought-out,fruit-veg-veg,happy-fish-face,home-scrap-scout,home-sweet-habitat,honey-bee,litter-bug,meerkat-mania,monarch,new-waste-pro,nosey-knows,oyster,plant-pilots,polar-bear,power-bright,real-or-not,safety-first,salad-bowl,school-scrap-scout,sea-turtle,send-a-smile,shower-sluesh,tag-it,tale-of-the-tail,tent-time,tunnel-adventures,twirl-n-swirl,twofer,wally-wall-wall,waste-pro,water-cycle,yawn-spawn";
            $games = explode(',', $games);
            foreach($games as $i=>$game) {
                $title = str_replace('-',' ', $game);
                $games[] = Game::create(array(
                    'title' => ucwords($title),
                    'description' => 'Description: '.$game,
                ));
                $this->command->info('Game: "'.$title.'" created!');
            }
        }
    }