<?php

namespace OrgTest;

use Application\Utils\Type\TypeInterface;
use Org\Organization;
use PHPUnit\Framework\TestCase;

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

        $org = new Organization();
        $org->exchangeArray([
            'org_id'      => 'foo-bar',
            'title'       => 'school of rock',
            'description' => 'are you ready to roll?',
            'type'        => TypeInterface::TYPE_DISTRICT,
        ]);
        $this->assertEquals(
            [
                'org_id'      => 'foo-bar',
                'title'       => 'school of rock',
                'description' => 'are you ready to roll?',
                'type'        => TypeInterface::TYPE_DISTRICT,
                'meta'        => [],
                'created'     => null,
                'updated'     => null,
                'deleted'     => null
            ],
            $org->getArrayCopy(),
            Organization::class . ' did not exchange the array correctly'
        );
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
            'type'        => TypeInterface::TYPE_GENERIC
        ];

        $org = new Organization();
        $org->exchangeArray($expected);

        $this->assertEquals($expected, $org->getArrayCopy());
    }
}
