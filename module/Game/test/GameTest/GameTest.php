<?php

namespace GameTest;

use Game\Game;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test GameTest
 *
 * @group Game
 */
class GameTest extends TestCase
{
    /**
     * Tests the game works fine when passed an empty array
     *
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $expected = [
            "game_id"     => null,
            "title"       => null,
            "description" => null,
            "meta"        => [],
            "created"     => null,
            "updated"     => null,
            "deleted"     => null,
            "coming_soon" => true,
        ];

        $org = new Game();
        $org->exchangeArray($expected);
        $this->assertEquals($expected, $org->getArrayCopy());
    }

    /**
     * Tests the data is hydrated to the game when passed data
     */
    public function testItShouldHydrateData()
    {
        $date = new \DateTime();

        $expected = [
            "game_id"     => "sea-turtle",
            "title"       => "Sea Turtle",
            "description" => "Sea Turtles are wondrous creatures! Get cool turtle facts",
            "meta"        => ["desktop" => "false", "unity" => "false"],
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
