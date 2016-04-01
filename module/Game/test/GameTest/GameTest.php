<?php

namespace GameTest;

use Game\Game;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test GameTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class GameTest extends TestCase
{
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $expected = [
            "game_id"     => null,
            "title"       => null,
            "description" => null,
            "created"     => null,
            "updated"     => null,
            "deleted"     => null,
            "coming_soon" => true,
        ];

        $org = new Game();
        $org->exchangeArray($expected);
        $this->assertEquals($expected, $org->getArrayCopy());
    }

    public function testItShouldHydrateData()
    {
        $date = new \DateTime();

        $expected = [
            "game_id"     => "sea-turtle",
            "title"       => "Sea Turtle",
            "description" => "Sea Turtles are wondrous creatures! Get cool turtle facts, play games and find out why they are endangered.",
            "created"     => $date->format(\DateTime::ISO8601),
            "updated"     => $date->format(\DateTime::ISO8601),
            "deleted"     => null,
            "coming_soon" => true,
        ];

        $org = new Game();
        $org->exchangeArray($expected);

        $this->assertEquals($expected, $org->getArrayCopy());
    }
}
