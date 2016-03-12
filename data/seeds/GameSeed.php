<?php

use Phinx\Seed\AbstractSeed;

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
        $date = new \DateTime();

        $games[] = [
            "game_id" => "polar-bear",
            "title" => "Polar Bear",
            "description" => "The magnificent Polar Bear is in danger of becoming extinct. Get the scoop and go offline for the science on how they stay warm!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "sea-turtle",
            "title" => "Sea Turtle",
            "description" => "Sea Turtles are wondrous creatures! Get cool turtle facts, play games and find out why they are endangered.",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "animal-id",
            "title" => "Animal ID",
            "description" => "Can you ID the different kinds of animals? Do you know what plants and animals belong together? Prove it and learn it right here!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "litter-bug",
            "title" => "Litterbug",
            "description" => "Sing it strong! Learn a great sing-a-long song while you work to save the environment! Doesn't get better!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "be-bright",
            "title" => "Be Bright",
            "description" => "Become a Light Saver agent of change! This music video will kick your inner superhero into high gear!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "fire",
            "title" => "FIRE!!!",
            "description" => "All about firefighters and firefighting theory. These are true heroes among us - maybe you will be one someday?",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "drought-out",
            "title" => "DroughtOUT",
            "description" => "Want to be part of the solution for the biggest issue in our world? You came to the right place! Starts right here!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "twirl-n-swirl",
            "title" => "Twirl n' Swirl",
            "description" => "Flushing isn't as simple as you think!  Avoid the plunger and help the environment!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "meerkat-mania",
            "title" => "Meerkat Mania",
            "description" => "You will learn about fascinating beasts, but don't be surprised to find so much more. A fun video gives you the scoop and the \"Meerkat Move!\" What's the move? Do the Action Item and discover how important you can be to your friends.",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        $games[] = [
            "game_id" => "printmaster",
            "title" => "Printmaster",
            "description" => "Is there a detective inside you? Find out as you learn about fingerprinting and go real world, taking and identifying prints in your own house!",
            "created" => $date->format('Y-m-d'),
            "updated" => $date->format('Y-m-d'),
            "deleted" => null
        ];

        foreach ($games as $game) {
            try {
                $this->table('games')
                    ->insert($game)
                    ->save();
            } catch (\PDOException $insertException) {
                // noop
            }
        }
    }
}
