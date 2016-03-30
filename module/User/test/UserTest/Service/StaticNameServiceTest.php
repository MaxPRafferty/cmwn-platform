<?php

namespace UserTest\Service;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Service\StaticNameService;
use User\UserName;

/**
 * Test StaticNameServiceTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class StaticNameServiceTest extends TestCase
{
    /**
     * @before
     */
    public function checkThatApplicationHasBeenBootstrapped()
    {
        $this->assertTrue(
            TestHelper::isBootstrapped(),
            'This test can only be run if the application has been bootstrapped'
        );
    }

    public function testItShouldThrowExceptionWhenListMissingKeys()
    {
        $leftThrown  = false;
        $rightThrown = false;

        try {
            StaticNameService::seedNames(['right' => []]);
        } catch (\InvalidArgumentException $leftException) {
            $this->assertEquals('Missing left or right values for names', $leftException->getMessage());
            $leftThrown = true;
        }
        try {
            StaticNameService::seedNames(['left' => []]);
        } catch (\InvalidArgumentException $rightException) {
            $this->assertEquals('Missing left or right values for names', $rightException->getMessage());
            $rightThrown = true;
        }

        $this->assertTrue($leftThrown && $rightThrown);
    }

    public function testItShouldThrowExceptionWhenKeysAreNotArrays()
    {
        $leftThrown  = false;
        $rightThrown = false;

        try {
            StaticNameService::seedNames(['left' => null, 'right' => []]);
        } catch (\InvalidArgumentException $leftException) {
            $this->assertEquals('left or right values must be an array', $leftException->getMessage());
            $leftThrown = true;
        }
        try {
            StaticNameService::seedNames(['left' => [], 'right' => null]);
        } catch (\InvalidArgumentException $rightException) {
            $this->assertEquals('left or right values must be an array', $rightException->getMessage());
            $rightThrown = true;
        }

        $this->assertTrue($leftThrown && $rightThrown);
    }

    public function testItShouldThrowExceptionWhenSelectingWrongPosition()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Invalid position: foo'
        );

        StaticNameService::getNames('foo');
    }

    /**
     * @covers  User\Service\StaticNameService::seedNames
     */
    public function testItShouldGenerateRandomNameAndNameShouldBeValidated()
    {
        $generatedName = StaticNameService::generateRandomName();
        $this->assertInstanceOf('\User\UserName', $generatedName, 'Invalid type returned');
        $this->assertTrue(StaticNameService::validateGeneratedName($generatedName), 'Generated Username is not valid');
    }

    public function testItShouldValidateFailureWhenGeneratedNameHasBadLeft()
    {
        $generatedName = StaticNameService::generateRandomName();
        $userName      = new UserName('foo', $generatedName->right);
        $this->assertFalse(StaticNameService::validateGeneratedName($userName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameHasBadRight()
    {
        $generatedName = StaticNameService::generateRandomName();
        $userName      = new UserName($generatedName->left, 'foo');
        $this->assertFalse(StaticNameService::validateGeneratedName($userName));
    }
}
