<?php

namespace UserTest\Service;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Service\StaticNameService;

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
        $this->assertInstanceOf('\stdClass', $generatedName);
        $this->assertNotEmpty($generatedName->leftName);
        $this->assertNotEmpty($generatedName->rightName);
        $this->assertEquals($generatedName->leftName . '_' . $generatedName->rightName, $generatedName->userName);
        $this->assertTrue(StaticNameService::validateGeneratedName($generatedName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameHasBadLeft()
    {
        $generatedName = StaticNameService::generateRandomName();
        $generatedName->leftName = 'foo';
        $this->assertFalse(StaticNameService::validateGeneratedName($generatedName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameHasBadRight()
    {
        $generatedName = StaticNameService::generateRandomName();
        $generatedName->rightName = 'foo';
        $this->assertFalse(StaticNameService::validateGeneratedName($generatedName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameHasMisMatchedUserName()
    {
        $generatedName = StaticNameService::generateRandomName();
        $generatedName->userName = 'foo';
        $this->assertFalse(StaticNameService::validateGeneratedName($generatedName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameMissingLeft()
    {
        $generatedName = StaticNameService::generateRandomName();
        unset($generatedName->leftName);
        $this->assertFalse(StaticNameService::validateGeneratedName($generatedName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameMissingRight()
    {
        $generatedName = StaticNameService::generateRandomName();
        unset($generatedName->rightName);
        $this->assertFalse(StaticNameService::validateGeneratedName($generatedName));
    }

    public function testItShouldValidateFailureWhenGeneratedNameMissingUserName()
    {
        $generatedName = StaticNameService::generateRandomName();
        unset($generatedName->userName);
        $this->assertFalse(StaticNameService::validateGeneratedName($generatedName));
    }
}
