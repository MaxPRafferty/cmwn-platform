<?php


namespace SecurityTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\PasswordValidator;

class PasswordValidatorTest extends TestCase
{

    /**
     * @dataProvider validPasswords
     */
    public function testItShouldPassWithStrongPassword($password)
    {
        $validator = new PasswordValidator();
        $this->assertTrue($validator->isValid($password));
    }

    /**
     * @dataProvider invalidPasswords
     */
    public function testItShouldFaileWithWeakPassword($password)
    {
        $validator = new PasswordValidator();
        $this->assertFalse($validator->isValid($password));
    }

    public function validPasswords()
    {
        return [
            ['a1234567'],
            ['a1234567sdblkjeier'],
            ['a1234567sdDSVWE'],
        ];
    }

    public function invalidPasswords()
    {
        return [
            ['a123456'],
            ['1234567'],
            ['abcd$%##@®'],
        ];
    }
}
