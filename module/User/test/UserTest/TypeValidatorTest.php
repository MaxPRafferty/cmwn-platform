<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\TypeValidator;
use User\UserInterface;

/**
 * Test TypeValidatorTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
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

    public function testItShouldPassWhenAdultIsPassedAndUsernamePassed()
    {
        $this->assertTrue(
            $this->validator->isValid(
                UserInterface::TYPE_ADULT,
                ['username' => 'manchuck']
            )
        );
    }

    public function testItShouldFailWhenChildIsPassedAndNoBirthdatePassed()
    {
        $this->assertFalse(
            $this->validator->isValid(
                UserInterface::TYPE_CHILD,
                []
            )
        );
    }

    public function testItShouldFailWhenAdultIsPassedAndUserNameIsMissing()
    {
        $this->assertFalse(
            $this->validator->isValid(
                UserInterface::TYPE_ADULT,
                []
            )
        );
    }

    public function testItShouldPassWhenChildIsPassedAndBirthdatePassed()
    {
        $this->assertTrue(
            $this->validator->isValid(
                UserInterface::TYPE_CHILD,
                ['birthdate' => '1982-05-13']
            )
        );
    }

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
