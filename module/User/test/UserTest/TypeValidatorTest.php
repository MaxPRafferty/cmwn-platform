<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\TypeValidator;
use User\UserInterface;

/**
 * Test TypeValidatorTest
 *
 * @group User
 * @group Validator
 */
class TypeValidatorTest extends TestCase
{
    /**
     * @var TypeValidator
     */
    protected $validator;

    /**
     * @before
     */
    public function setUpValidator()
    {
        $this->validator = new TypeValidator();
    }

    /**
     * @test
     */
    public function testItShouldPassWhenAdultIsPassedAndUsernamePassed()
    {
        $this->assertTrue(
            $this->validator->isValid(
                UserInterface::TYPE_ADULT,
                ['username' => 'manchuck']
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenChildIsPassedAndNoBirthdatePassed()
    {
        $this->assertFalse(
            $this->validator->isValid(
                UserInterface::TYPE_CHILD,
                []
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenAdultIsPassedAndUserNameIsMissing()
    {
        $this->assertFalse(
            $this->validator->isValid(
                UserInterface::TYPE_ADULT,
                []
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldPassWhenChildIsPassedAndBirthdatePassed()
    {
        $this->assertTrue(
            $this->validator->isValid(
                UserInterface::TYPE_CHILD,
                ['birthdate' => '1982-05-13']
            )
        );
    }

    /**
     * @test
     */
    public function testItShouldFailWhenInvalidTypePassed()
    {
        $this->assertFalse(
            $this->validator->isValid(
                'foo',
                []
            )
        );
    }
}
