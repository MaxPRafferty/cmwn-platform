<?php

namespace UserTest;

use PHPUnit\Framework\TestCase;
use User\UserName;
use User\UserNameValidator;

/**
 * Test UserNameValidatorTest
 *
 * @group User
 * @group Validator
 */
class UserNameValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldValidateCorrectly()
    {
        $this->markTestSkipped('Fix the UserNameValidator');
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
