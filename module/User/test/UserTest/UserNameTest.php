<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\UserName;

/**
 * Class UserNameTest
 * @package UserTest
 */
class UserNameTest extends TestCase
{
    public function testItShouldAddLeftAndRightNumbers()
    {
        $userName = new UserName('foo', 'bar');

        $userName->setValues(1, 1);
        $this->assertEquals('foo-bar002', $userName->userName);

        $userName->setValues(5, 8);
        $this->assertEquals('foo-bar013', $userName->userName);
    }

    public function testItShouldKeepLeftAndRightValuesAboveOne()
    {
        $userName = new UserName('foo', 'bar');

        $userName->setValues(0, 0);
        $this->assertEquals('foo-bar002', $userName->userName);
    }
    
    public function testItShouldThrowExceptionWhenAccessingInvalidProperty()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Invalid Property: foo'
        );

        $userName = new UserName('foo', 'bar');
        $userName->foo;
    }
}
