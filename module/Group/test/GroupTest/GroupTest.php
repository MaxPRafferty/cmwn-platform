<?php

namespace GroupTest;

use Group\Group;
use Org\Organization;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test GroupTest
 *
 * @group Group
 */
class GroupTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $expected = [
            'group_id'        => null,
            'organization_id' => null,
            'title'           => null,
            'description'     => null,
            'type'            => null,
            'meta'            => [],
            'head'            => 0,
            'tail'            => 0,
            'depth'           => 0,
            'created'         => null,
            'updated'         => null,
            'deleted'         => null,
            'external_id'     => null,
            'parent_id'       => null,
            'network_id'      => 'foo-bar',
        ];

        $adult = new Group();
        $adult->exchangeArray($expected);
        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    /**
     * @test
     */
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
            'head'            => 1,
            'tail'            => 2,
            'depth'           => 3,
            'created'         => $date->format(\DateTime::ISO8601),
            'updated'         => $date->format(\DateTime::ISO8601),
            'deleted'         => $date->format(\DateTime::ISO8601),
            'external_id'     => 'foo-bar',
            'parent_id'       => null,
            'network_id'      => 'foo-bar',
        ];

        $adult = new Group();
        $adult->exchangeArray($expected);

        $this->assertEquals($expected, $adult->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldReportRoot()
    {
        $group = new Group();
        $this->assertEquals(
            1,
            $group->getHead(),
            'Head value for group must be set to 1'
        );

        $this->assertEquals(
            2,
            $group->getTail(),
            'Tail value for group must be set to 2'
        );

        $this->assertTrue(
            $group->isRoot(),
            'Group must be root when head value is 1'
        );

        $this->assertFalse(
            $group->hasChildren(),
            'Group must not have children when tail value is 1 greater than head'
        );


        $group->setTail(4);
        $this->assertTrue(
            $group->hasChildren(),
            'Group must report children when tail is not 1 greater than head'
        );

        $group->setHead(3);
        $this->assertFalse(
            $group->isRoot(),
            'Group must not be root when head value not 1'
        );
    }

    /**
     * @test
     */
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
