<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\StaticUserFactory;
use User\UserInterface;

/**
 * Test StaticUserFactoryTest
 *
 * @group User
 * @group Hydrator
 */
class StaticUserFactoryTest extends TestCase
{
    /**
     * @var array
     */
    protected $userData = [];

    /**
     * @before
     */
    public function setUpData()
    {
        $date = new \DateTime();
        $this->userData = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => 'male',
            'birthdate'   => $date->format(\DateTime::ISO8601),
            'created'     => $date->format(\DateTime::ISO8601),
            'updated'     => $date->format(\DateTime::ISO8601),
            'deleted'     => $date->format(\DateTime::ISO8601),
            'type'        => UserInterface::TYPE_ADULT
        ];
    }

    /**
     * @test
     */
    public function testItShouldReturnAdultWhenAdultIsPassedInArray()
    {
        $user = StaticUserFactory::createUser($this->userData);
        $this->assertInstanceOf('User\Adult', $user);
        $this->assertEquals($this->userData['username'], $user->getUserName());
    }

    /**
     * @test
     */
    public function testItShouldReturnAdultWhenDataIsArrayObject()
    {
        $user = StaticUserFactory::createUser(new \ArrayObject($this->userData));
        $this->assertInstanceOf('User\Adult', $user);
        $this->assertEquals($this->userData['username'], $user->getUserName());
    }

    /**
     * @test
     */
    public function testItShouldReturnAdultWhenAdultIsPassedAsParameter()
    {
        unset($this->userData['type']);
        
        $user = StaticUserFactory::createUser($this->userData, UserInterface::TYPE_ADULT);
        $this->assertInstanceOf('User\Adult', $user);
        $this->assertEquals($this->userData['username'], $user->getUserName());
    }

    /**
     * @test
     */
    public function testItShouldReturnChildWhenChildIsPassedInArray()
    {
        $this->userData['type'] = UserInterface::TYPE_CHILD;
        $user = StaticUserFactory::createUser($this->userData);
        $this->assertInstanceOf('User\Child', $user);
        $this->assertEquals($this->userData['username'], $user->getUserName());
    }

    /**
     * @test
     */
    public function testItShouldReturnChildWhenChildIsPassedIAsParameter()
    {
        unset($this->userData['type']);
        
        $user = StaticUserFactory::createUser($this->userData, UserInterface::TYPE_CHILD);
        $this->assertInstanceOf('User\Child', $user);
        $this->assertEquals($this->userData['username'], $user->getUserName());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenInvalidTypePassedInArray()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid user type: foo'
        );

        StaticUserFactory::createUser(['type' => 'foo']);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenInvalidTypePassedAsParameter()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid user type: foo'
        );

        StaticUserFactory::createUser([], 'foo');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenDataIsInvalid()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Data must be an array or ArrayObject'
        );

        StaticUserFactory::createUser('foo');
    }
}
