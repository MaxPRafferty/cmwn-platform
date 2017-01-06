<?php

namespace UserTest;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;
use User\Service\StaticNameService;

/**
 * Test ChildTest
 *
 * @group User
 */
class ChildTest extends TestCase
{
    /**
     * @before
     */
    public function checkThatApplicationHasBeenBootstrapped()
    {
        $this->markTestSkipped('Fix the StaticNameService');
        $this->assertTrue(
            TestHelper::isBootstrapped(),
            'This test can only be run if the application has been bootstrapped'
        );
    }

    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $name = StaticNameService::generateRandomName();
        $expected = [
            'user_id'     => null,
            'username'    => $name->userName,
            'email'       => $name->userName . '@changemyworldnow.com',
            'first_name'  => null,
            'middle_name' => null,
            'last_name'   => null,
            'gender'      => null,
            'birthdate'   => null,
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'type'        => Child::TYPE_CHILD,
            'meta'        => [],
            'external_id' => null,
        ];

        $adult = new Child();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldHydrateData()
    {
        $date = new \DateTime();
        $name = StaticNameService::generateRandomName();
        $expected = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => $name->userName,
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => Child::GENDER_MALE,
            'birthdate'   => $date->format("Y-m-d H:i:s"),
            'created'     => $date->format("Y-m-d H:i:s"),
            'updated'     => $date->format("Y-m-d H:i:s"),
            'deleted'     => $date->format("Y-m-d H:i:s"),
            'type'        => Child::TYPE_CHILD,
            'meta'        => [],
            'external_id' => 'foo-bar'
        ];

        $adult = new Child();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldGenerateRandomNameWhenUserNameIsNotSet()
    {
        $child = new Child();
        $this->assertFalse($child->isNameGenerated());
        $this->assertNull($child->getGeneratedName());

        $this->assertNotEmpty($child->getUserName());
        $this->assertNotNull($child->getGeneratedName());
        $this->assertTrue($child->isNameGenerated());
    }

    /**
     * @test
     */
    public function testItShouldReportNameNotGeneratedWhenSet()
    {
        $child = new Child();

        $child->setUserName('foo_bar');
        $this->assertFalse($child->isNameGenerated());
        $this->assertNull($child->getGeneratedName());
    }

    /**
     * @test
     */
    public function testItShouldNotChangeTheUserNameAfterItHasBeenSet()
    {
        $child = new Child();
        $child->setUserName('manchuck');
        $child->setUserName('foo_bar');
        $this->assertEquals('manchuck', $child->getUserName());
    }
}
