<?php

namespace GroupTest;

use Application\Utils\Type\TypeInterface;
use Group\Group;
use Org\Organization;
use PHPUnit\Framework\TestCase;

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
        $adult = new Group();
        $adult->exchangeArray([
            'group_id'        => 'foo-bar',
            'organization_id' => 'fizz-buzz',
            'title'           => 'my class',
            'description'     => 'This is the best class ever',
            'type'            => TypeInterface::TYPE_CLASS,
            'meta'            => [],
            'head'            => 0,
            'tail'            => 0,
            'depth'           => 0,
            'created'         => null,
            'updated'         => null,
            'deleted'         => null,
            'external_id'     => 'baz-bat',
            'network_id'      => 'foo-bar',
        ]);

        $this->assertEquals(
            [
                'group_id'        => 'foo-bar',
                'organization_id' => 'fizz-buzz',
                'title'           => 'my class',
                'description'     => 'This is the best class ever',
                'type'            => TypeInterface::TYPE_CLASS,
                'meta'            => [],
                'head'            => 0,
                'tail'            => 0,
                'depth'           => 0,
                'created'         => null,
                'updated'         => null,
                'deleted'         => null,
                'external_id'     => 'baz-bat',
                'network_id'      => 'foo-bar',
                'parent_id'       => null,
            ],
            $adult->getArrayCopy(),
            Group::class . ' did not exchange data from array correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldHydrateData()
    {
        $date  = new \DateTime();
        $group = new Group();
        $group->exchangeArray([
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
            'network_id'      => 'foo-bar',
        ]);

        $this->assertEquals(
            [
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
                'network_id'      => 'foo-bar',
                'parent_id'       => null,
            ],
            $group->getArrayCopy(),
            Group::class . ' did not hydrate correctly with dates'
        );
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
            Group::class . ' Head value for group must be set to 1'
        );

        $this->assertEquals(
            2,
            $group->getTail(),
            Group::class . ' Tail value for group must be set to 2'
        );

        $this->assertTrue(
            $group->isRoot(),
            Group::class . ' Group must be root when head value is 1'
        );

        $this->assertFalse(
            $group->hasChildren(),
            Group::class . ' must not have children when tail value is 1 greater than head'
        );

        $group->setTail(4);
        $this->assertTrue(
            $group->hasChildren(),
            Group::class . ' must report children when tail is not 1 greater than head'
        );

        $group->setHead(3);
        $this->assertFalse(
            $group->isRoot(),
            Group::class . ' must not be root when head value not 1'
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
            Group::class . ' did not set the org Id from an organization'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachToOrganizationCorrectly()
    {
        $group = new Group();
        $org   = new Organization();
        $org->setOrgId('foo-bar');

        $group->attachToOrganization($org);
        $this->assertEquals(
            'foo-bar',
            $group->getOrganizationId(),
            Group::class . ' did not attach to the org Id from an organization'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachToParentGroupCorrectly()
    {
        $child = new Group();
        $parent = new Group();
        $parent->setGroupId('fizz-buzz')
            ->setNetworkId('foo-bar');

        $child->attachToGroup($parent);

        $this->assertEquals(
            'foo-bar',
            $child->getNetworkId(),
            Group::class . ' did not take the network Id from the parent'
        );

        $this->assertEquals(
            'fizz-buzz',
            $child->getParentId(),
            Group::class . ' did not take the parent Id when attaching'
        );
    }
}
