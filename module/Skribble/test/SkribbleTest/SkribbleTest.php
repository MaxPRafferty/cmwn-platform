<?php

namespace SkribbleTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Skribble;
use User\Child;
use Zend\Json\Json;

/**
 * Test SkribbleTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SkribbleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeAbleToHydrateFromJson()
    {
        $json         = file_get_contents(__DIR__ . '/_files/valid.skribble.json');
        $skribbleData = Json::decode($json, Json::TYPE_ARRAY);
        $skribble     = new Skribble($skribbleData);

        $this->assertEquals($skribbleData, $skribble->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldTakeInUserForCreatedByAndFriend()
    {
        $skribble = new Skribble();

        $created = new Child(['user_id' => 'foo-bar']);
        $friend  = new Child(['user_id' => 'baz-bat']);

        $skribble->setCreatedBy($created);
        $skribble->setFriendTo($friend);

        $skribbleData = $skribble->getArrayCopy();
        $this->assertEquals(
            'foo-bar',
            $skribbleData['created_by'],
            'Skribble created_by did not convert the user to the id'
        );

        $this->assertEquals(
            'baz-bat',
            $skribbleData['friend_to'],
            'Skribble created_by did not convert the friend to the id'
        );
    }

    /**
     * @test
     */
    public function testItShouldTakeJsonDataForSkribbleRules()
    {
        $json         = file_get_contents(__DIR__ . '/_files/valid.skribble.json');
        $skribbleData = Json::decode($json, Json::TYPE_ARRAY);
        $rules        = Json::encode($skribbleData['rules']);
        $skribble     = new Skribble();

        $skribble->setRules($rules);
        $this->assertEquals(
            $skribbleData['rules'],
            $skribble->getRules()->getArrayCopy(),
            'Skribble did not take in Json string for Rules'
        );
    }

    /**
     * @test
     */
    public function testItShouldTakeInArrayForSkribbleRules()
    {
        $json         = file_get_contents(__DIR__ . '/_files/valid.skribble.json');
        $skribbleData = Json::decode($json, Json::TYPE_ARRAY);
        $rules        = $skribbleData['rules'];
        $skribble     = new Skribble();

        $skribble->setRules($rules);
        $this->assertEquals(
            $skribbleData['rules'],
            $skribble->getRules()->getArrayCopy(),
            'Skribble did not take in Json string for Rules'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenInvalidRulesPassedIn()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            'Only arrays, Json or instances of SkribbleRules can be set for rules'
        );

        $skribble = new Skribble();
        $skribble->setRules(new \stdClass());
    }
}
