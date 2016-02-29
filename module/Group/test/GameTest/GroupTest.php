<?php

namespace GroupTest;

use Group\Group;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test GameTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class GroupTest extends TestCase
{
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $expected = [
            'group_id'        => null,
            'organization_id' => null,
            'title'           => null,
            'description'     => null,
            'type'            => null,
            'meta'            => [],
            'left'            => 0,
            'right'           => 0,
            'depth'           => 0,
            'created'         => null,
            'updated'         => null,
            'deleted'         => null,
        ];

        $adult = new Group();
        $adult->exchangeArray($expected);
        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    public function testItShouldHydrateData()
    {
        $date = new \DateTime();

        $expected = [
            'group_id'        => 'abcd-efgh-ijklm-nop',
            'organization_id' => 'abcd-efgh-ijklm-nop',
            'title'           => 'manchuck\s group',
            'description'     => 'My Awesome group',
            'type'            => 'school',
            'meta'            => [],
            'left'            => 1,
            'right'           => 2,
            'depth'           => 3,
            'created'         => $date->format(\DateTime::ISO8601),
            'updated'         => $date->format(\DateTime::ISO8601),
            'deleted'         => $date->format(\DateTime::ISO8601),
        ];

        $adult = new Group();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }
}
