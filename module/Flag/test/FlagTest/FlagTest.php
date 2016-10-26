<?php


namespace FlagTest;

use Flag\Flag;
use \PHPUnit_Framework_TestCase as TestCase;
use Ramsey\Uuid\Uuid;
use User\Child;
use User\UserInterface;

/**
 * Class FlagTest
 * @package FlagTest
 * @group Flag
 */
class FlagTest extends TestCase
{
    /**
     * @var array
     */
    protected $flagData;

    /**
     * @before
     */
    public function setUpFlagData()
    {
        $this->flagData = [
            'flag_id' => 'foobar',
            'flagger' => new Child(['user_id'=>'english_student']),
            'flaggee' => new Child(['user_id'=>'math_student']),
            'reason'  => 'foo',
            'url'     => '/bar',
        ];
    }

    /**
     * @test
     */
    public function testItShouldExchangeArrayWhenUsersArePassed()
    {
        $flag = new Flag();
        $flag->exchangeArray($this->flagData);
        $this->assertEquals('foobar', $flag->getFlagId());
        $this->assertEquals('english_student', $flag->getFlagger()->getUserId());
        $this->assertEquals('math_student', $flag->getFlaggee()->getUserId());
        $this->assertEquals('foo', $flag->getReason());
        $this->assertEquals('/bar', $flag->getUrl());
    }

    /**
     * @test
     */
    public function testItShouldExtractWithValidFlagData()
    {
        $flag = new Flag($this->flagData);
        $data = $this->flagData;
        $data['flagger'] = $this->flagData['flagger']->getArrayCopy();
        $data['flaggee'] = $this->flagData['flaggee']->getArrayCopy();
        $this->assertEquals($data, $flag->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $data = [
            'flag_id' => null,
            'flagger' => null,
            'flaggee' => null,
            'reason'  => null,
            'url'     => null,
        ];

        $flag = new Flag();
        $flag->exchangeArray($data);
        $this->assertEquals($flag->getArrayCopy(), $data);
    }

    /**
     * @test
     */
    public function testItShouldConvertFlagIdToString()
    {
        $data = $this->flagData;
        $data['flag_id'] = Uuid::uuid1();
        $flag = new Flag($data);
        $this->assertTrue(is_string($flag->getFlagId()));
    }

    /**
     * @test
     */
    public function testItShouldCreateUserIfFlaggerAndFlaggeeAreArrays()
    {
        $data = $this->flagData;
        $data['flagger'] = $this->flagData['flagger']->getArrayCopy();
        $data['flaggee'] = $this->flagData['flaggee']->getArrayCopy();
        $flag = new Flag($data);

        $this->assertInstanceOf(UserInterface::class, $flag->getFlagger());
        $this->assertInstanceOf(UserInterface::class, $flag->getFlaggee());
    }
}
