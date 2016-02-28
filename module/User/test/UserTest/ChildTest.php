<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;

/**
 * Test AdultTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class ChildTest extends TestCase
{
    public function testItShouldExtractAndhydrateWithNulls()
    {
        $expected = [
            'user_id'     => '',
            'username'    => null,
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

        $expected = [
            'user_id'     => 'abcd-efgh-ijklm-nop',
            'username'    => 'manchuck',
            'email'       => 'chuck@manchuck.com',
            'first_name'  => 'Charles',
            'middle_name' => 'Henry',
            'last_name'   => 'Reeves',
            'gender'      => Child::GENDER_MALE,
            'birthdate'   => $date->format(\DateTime::ISO8601),
            'created'     => $date->format(\DateTime::ISO8601),
            'updated'     => $date->format(\DateTime::ISO8601),
            'deleted'     => $date->format(\DateTime::ISO8601),
            'type'        => Child::TYPE_CHILD,
            'meta'        => []
        ];

        $adult = new Child();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }
}
