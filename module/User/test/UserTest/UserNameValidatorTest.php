<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\UserName;
use User\UserNameValidator;

/**
 * Exception UserNameValidatorTest
 */
class UserNameValidatorTest extends TestCase
{
    public function testItShouldValidateCorrectly()
    {
        $userName = new UserName('awesome', 'otter');
        $validator = new UserNameValidator();
        $this->assertTrue(
            $validator->isValid($userName),
            'Validator did not validate the name with a UserName Object'
        );

        $this->assertTrue(
            $validator->isValid($userName->userName),
            'Validator did not valid the name with a string'
        );

        $this->assertFalse(
            $validator->isValid('foo/bar'),
            'Validator did not fail with invalid string'
        );

        $this->assertFalse(
            $validator->isValid(new UserName('foo', 'bar')),
            'Validator did not fail with invalid UserName Object'
        );
    }
}
