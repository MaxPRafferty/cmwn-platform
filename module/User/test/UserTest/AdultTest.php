<?php

namespace UserTest;

use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;

/**
 * Test AdultTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class AdultTest extends TestCase
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
            'type'        => Adult::TYPE_ADULT,
            'meta'        => []
        ];

        $adult = new Adult();
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
            'gender'      => Adult::GENDER_MALE,
            'birthdate'   => $date->format(\DateTime::ISO8601),
            'created'     => $date->format(\DateTime::ISO8601),
            'updated'     => $date->format(\DateTime::ISO8601),
            'deleted'     => $date->format(\DateTime::ISO8601),
            'type'        => Adult::TYPE_ADULT,
            'meta'        => []
        ];

        $adult = new Adult();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }
}
