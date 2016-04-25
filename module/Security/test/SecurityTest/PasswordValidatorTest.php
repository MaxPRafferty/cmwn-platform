<?php


namespace SecurityTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\PasswordValidator;

/**
 * Test PasswordValidatorTest
 *
 * @group Security
 * @group Validator
 * @group Authentication
 */
class PasswordValidatorTest extends TestCase
{
    /**
     * @dataProvider validPasswords
     * @param $password
     * @test
     */
    public function testItShouldPassWithStrongPassword($password)
    {
        $validator = new PasswordValidator();
        $this->assertTrue($validator->isValid($password));
    }

    /**
     * @dataProvider invalidPasswords
     * @param $password
     * @test
     */
    public function testItShouldFailsWithWeakPassword($password)
    {
        $validator = new PasswordValidator();
        $this->assertFalse($validator->isValid($password));
    }

    /**
     * @return array
     */
    public function validPasswords()
    {
        return [
            ['a1234567'],
            ['a1234567sdblkjeier'],
            ['a1234567sdDSVWE'],
        ];
    }

    /**
     * @return array
     */
    public function invalidPasswords()
    {
        return [
            ['a123456'],
            ['1234567'],
            ['abcd$%##@®'],
        ];
    }
}
