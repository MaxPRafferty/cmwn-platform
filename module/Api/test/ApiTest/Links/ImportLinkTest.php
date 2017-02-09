<?php

namespace ApiTest\Links;

use Api\Links\ImportLink;
use Group\Group;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test ImportLinkTest
 *
 * @group API
 * @group Links
 * @group Import
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImportLinkTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHaveCorrectLabelWithGroup()
    {
        $group = new Group(['group_id' => 'foo-bar']);
        $link = new ImportLink($group);

        $this->assertEquals(
            ['label' => 'Import Users'],
            $link->getProps(),
            'Label is incorrect for import link'
        );

        $this->assertEquals(
            ['group_id' => 'foo-bar'],
            $link->getRouteParams(),
            'Group Id is not set for the route on Import Link'
        );

        $this->assertEquals(
            'api.rest.import',
            $link->getRoute(),
            'Incorrect Route for import link'
        );
    }

    /**
     * @test
     */
    public function testItShouldHaveCorrectSettingsWithGroupId()
    {
        $link = new ImportLink('foo-bar');

        $this->assertEquals(
            ['label' => 'Import Users'],
            $link->getProps(),
            'Label is incorrect for import link'
        );

        $this->assertEquals(
            ['group_id' => 'foo-bar'],
            $link->getRouteParams(),
            'Group Id is not set for the route on Import Link'
        );

        $this->assertEquals(
            'api.rest.import',
            $link->getRoute(),
            'Incorrect Route for import link'
        );
    }
}
