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
        DB::table("games")->delete();

        $this->command->info("Creating Games!");

        $games[] = Game::create(array(
            "uuid" => "polar-bear",
            "title" => "Polar Bear",
            "description" => "The magnificent Polar Bear is in danger of becoming extinct. Get the scoop and go offline for the science on how they stay warm!"
        ));

        $games[] = Game::create(array(
            "uuid" => "sea-turtle",
            "title" => "Sea Turtle",
            "description" => "Sea Turtles are wondrous creatures! Get cool turtle facts, play games and find out why they are endangered."
        ));

        $games[] = Game::create(array(
            "uuid" => "animal-id",
            "title" => "Animal ID",
            "description" => "Can you ID the different kinds of animals? Do you know what plants and animals belong together? Prove it and learn it right here!"
        ));

        $games[] = Game::create(array(
            "uuid" => "litter-bug",
            "title" => "Litterbug",
            "description" => "Sing it strong! Learn a great sing-a-long song while you work to save the environment! Doesn't get better!"
        ));

        $games[] = Game::create(array(
            "uuid" => "be-bright",
            "title" => "Be Bright",
            "description" => "Become a Light Saver agent of change! This music video will kick your inner superhero into high gear!"
        ));

        $games[] = Game::create(array(
            "uuid" => "fire",
            "title" => "FIRE!!!",
            "description" => "All about firefighters and firefighting theory. These are true heroes among us - maybe you will be one someday?"
        ));

        $games[] = Game::create(array(
            "uuid" => "drought-out",
            "title" => "DroughtOUT",
            "description" => "Want to be part of the solution for the biggest issue in our world? You came to the right place! Starts right here!"
        ));

        $games[] = Game::create(array(
            "uuid" => "twirl-n-swirl",
            "title" => "Twirl n' Swirl",
            "description" => "Flushing isn't as simple as you think!  Avoid the plunger and help the environment!"
        ));

        $games[] = Game::create(array(
            "uuid" => "meerkat-mania",
            "title" => "Meerkat Mania",
            "description" => "You will learn about fascinating beasts, but don't be surprised to find so much more. A fun video gives you the scoop and the \"Meerkat Move!\" What's the move? Do the Action Item and discover how important you can be to your friends."
        ));

        $this->command->info("Games Created!");
    }
}
