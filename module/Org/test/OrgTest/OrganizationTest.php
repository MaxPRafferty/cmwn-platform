<?php

namespace OrgTest;

use Org\Organization;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test OrganizationTest
 *
 * @group Organization
 */
class OrganizationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldExtractAndHydrateWithNulls()
    {
        $expected = [
            'org_id'      => null,
            'title'       => null,
            'description' => null,
            'meta'        => [],
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'type'        => null,
        ];

        $org = new Organization();
        $org->exchangeArray($expected);
        $this->assertEquals($expected, $org->getArrayCopy());
    }

    /**
     * @test
     */
    public function testItShouldHydrateData()
    {
        $date = new \DateTime();

        $expected = [
            'org_id'      => 'abcd-efgh-ijklm-nop',
            'title'       => 'manchuck',
            'description' => 'chuck@manchuck.com',
            'meta'        => [],
            'created'     => $date->format(\DateTime::ISO8601),
            'updated'     => $date->format(\DateTime::ISO8601),
            'deleted'     => $date->format(\DateTime::ISO8601),
            'type'        => 'test'
        ];

        $org = new Organization();
        $org->exchangeArray($expected);

        $this->assertEquals($expected, $org->getArrayCopy());
    }
}
