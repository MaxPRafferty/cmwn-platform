<?php

namespace UserTest;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;
use User\Service\StaticNameService;

/**
 * Test AdultTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class ChildTest extends TestCase
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

    public function testItShouldExtractAndHydrateWithNulls()
    {
        $name = StaticNameService::generateRandomName();
        $expected = [
            'user_id'     => '',
            'username'    => $name->userName,
            'email'       => null,
            'first_name'  => null,
            'middle_name' => null,
            'last_name'   => null,
            'gender'      => null,
            'birthdate'   => null,
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'type'        => Child::TYPE_CHILD,
            'meta'        => []
        ];

        $adult = new Child();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }

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
            'birthdate'   => $date->getTimestamp(),
            'created'     => $date->getTimestamp(),
            'updated'     => $date->getTimestamp(),
            'deleted'     => $date->getTimestamp(),
            'type'        => Child::TYPE_CHILD,
            'meta'        => []
        ];

        $adult = new Child();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    public function testItShouldGenerateRandomNameWhenUserNameIsNotSet()
    {
        $child = new Child();
        $this->assertFalse($child->isNameGenerated());
        $this->assertNull($child->getGenratedName());

        $this->assertNotEmpty($child->getUserName());
        $this->assertNotNull($child->getGenratedName());
        $this->assertTrue($child->isNameGenerated());

        $child->setUserName('foo_bar');
        $this->assertFalse($child->isNameGenerated());
        $this->assertNull($child->getGenratedName());
    }
}
