<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Phinx\Seed\AbstractSeed;

/**
 * Class FlipsSeed
 *
 * @codingStandardsIgnoreStart
 * @deprecated
 * @todo Remove me once we have flips god mode
 */
class FlipsSeed extends AbstractSeed
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
        $flips   = [];
        $flips[] = [
            'flip_id'     => 'polar-bear',
            'title'       => 'Polar Bear',
            'description' => 'The magnificent Polar Bear is in danger of becoming extinct. Get the scoop and go offline for the science on how they stay warm!',
        ];

        $flips[] = [
            'flip_id'     => 'sea-turtle',
            'title'       => 'Sea Turtle',
            'description' => 'Sea Turtles are wondrous creatures! Get cool turtle facts, play flips and find out why they are endangered.',
        ];

        $flips[] = [
            'flip_id'     => 'animal-id',
            'title'       => 'Animal ID',
            'description' => 'Can you ID the different kinds of animals? Do you know what plants and animals belong together? Prove it and learn it right here!',
        ];

        $flips[] = [
            'flip_id'     => 'litter-bug',
            'title'       => 'Litterbug',
            'description' => 'Sing it strong! Learn a great sing-a-long song while you work to save the environment! Doesn\'t get better!',
        ];

        $flips[] = [
            'flip_id'     => 'be-bright',
            'title'       => 'Be Bright',
            'description' => 'Become a Light Saver agent of change! This music video will kick your inner superhero into high gear!',
        ];

        $flips[] = [
            'flip_id'     => 'fire',
            'title'       => 'FIRE!!!',
            'description' => 'All about firefighters and firefighting theory. These are true heroes among us - maybe you will be one someday?',
        ];

        $flips[] = [
            'flip_id'     => 'drought-out',
            'title'       => 'DroughtOUT',
            'description' => 'Want to be part of the solution for the biggest issue in our world? You came to the right place! Starts right here!',
        ];

        $flips[] = [
            'flip_id'     => 'twirl-n-swirl',
            'title'       => 'Twirl n\' Swirl',
            'description' => 'Flushing isn\'t as simple as you think!  Avoid the plunger and help the environment!',
        ];

        $flips[] = [
            'flip_id'     => 'meerkat-mania',
            'title'       => 'Meerkat Mania',
            'description' => 'You will learn about fascinating beasts, but don\'t be surprised to find so much more. A fun video gives you the scoop and the \'Meerkat Move!\' What\'s the move? Do the Action Item and discover how important you can be to your friends.',
        ];

        $flips[] = [
            'flip_id'     => 'printmaster',
            'title'       => 'Printmaster',
            'description' => 'Is there a detective inside you? Find out as you learn about fingerprinting and go real world, taking and identifying prints in your own house!',
        ];

        $flips[] = [
            'flip_id'     => 'happy-fish-face',
            'title'       => 'Happy Fish Face',
            'description' => 'Find out how a fish feels! Get the scoop on water pollution and have fun doing it! Grab a net - we\'re cleaning up!',
        ];

        $flips[] = [
            'flip_id'     => 'all-about-you',
            'title'       => 'All About You',
            'description' => 'This survey is All About You: your favorite things, what makes you happy or sad, and a lot more!',
        ];

        /*
         * unity flips
         */
        $flips[] = [
            'flip_id'     => 'pedal-pusher',
            'title'       => 'Pedal Pusher',
            'description' => 'Jump, flip and win points! Guide your bike through the terrain.',
        ];

        $flips[] = [
            'flip_id'     => 'salad-rain',
            'title'       => 'Salad Rain',
            'description' => 'Be a Salad Chef!  Make tasty salads by following the recipes!',
        ];

        $flips[] = [
            'flip_id'     => 'turtle-hurdle',
            'title'       => 'Turtle Hurdle',
            'description' => 'Sea Turtles are at risk but you will guide yours to safety!',
        ];

        $flips[] = [
            'flip_id'     => 'twirling-tower',
            'title'       => 'Twirling Tower',
            'description' => 'Weather can change at any moment! Stay ahead of the tornado!',
        ];

        $flips[] = [
            'flip_id'     => 'bloom-boom',
            'title'       => 'Bloom Boom',
            'description' => 'Be a pollinating pilot! Direct your pollinators to get a field of flowers!',
        ];

        $flips[] = [
            'flip_id'     => 'carbon-catcher',
            'title'       => 'Carbon Catcher',
            'description' => 'Bubble, bubble, carbon is trouble! Clean up the air by converting emissions!',
        ];

        $flips[] = [
            'flip_id'     => 'waterdrop',
            'title'       => 'Waterdrop',
            'description' => 'Drip, drip drop! Water conservation is critical. Harness the power and score big!',
        ];

        $flips[] = [
            'flip_id'     => 'reef-builder',
            'title'       => 'Reef Builder',
            'description' => 'Create an ocean reef habitat for sea creatures and win big doing it!',
        ];

        $flips[] = [
            'flip_id'     => 'rockin-room',
            'title'       => 'Rockin Room',
            'description' => 'Rocks! Rooms! What more could you ask for?!',
        ];

        $flips[] = [
            'flip_id'     => 'monarch',
            'title'       => 'Monarchs',
            'description' => 'Monarch Butterflies are crucial for the environment yet they are endangered! This is your spot!',
        ];

        $flips[] = [
            'flip_id'     => 'first-time-login',
            'title'       => 'Login for the first time',
            'description' => 'Login for the first time',
        ];

        $table = $this->table('flips');
        foreach ($flips as $flip) {
            try {
                $table
                    ->insert($flip)
                    ->save();
            } catch (\PDOException $exception) {
                if ($exception->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        'Got Exception When inserting flip: ' . $exception->getMessage()
                    );
                }
            }
            $table->setData([]);
        }
    }
}
