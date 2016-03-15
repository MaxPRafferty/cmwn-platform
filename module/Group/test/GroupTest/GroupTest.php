<?php

namespace GroupTest;

use Group\Group;
use Org\Organization;
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
            'external_id'     => null,
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
            'external_id'     => 'foo-bar',
        ];

        $adult = new Group();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    public function testItShouldReportRoot()
    {
        $group = new Group();
        $this->assertEquals(
            1,
            $group->getLeft(),
            'Left value for group must be set to 1'
        );

        $this->assertEquals(
            2,
            $group->getRight(),
            'Right value for group must be set to 2'
        );

        $this->assertTrue(
            $group->isRoot(),
            'Group must be root when left value is 1'
        );

        $this->assertFalse(
            $group->hasChildren(),
            'Group must not have children when right value is 1 greater than left'
        );


        $group->setRight(4);
        $this->assertTrue(
            $group->hasChildren(),
            'Group must report children when right is not 1 greater than left'
        );

        $group->setLeft(3);
        $this->assertFalse(
            $group->isRoot(),
            'Group must not be root when left value not 1'
        );
    }

    public function testItShouldSetOrgIdWhenOrganizationPassed()
    {
        $group = new Group();
        $org   = new Organization();
        $org->setOrgId('foo-bar');

        $group->setOrganizationId($org);
        $this->assertEquals(
            'foo-bar',
            $group->getOrganizationId(),
            'Group did not set the org Id from an organization'
        );
    }
}
