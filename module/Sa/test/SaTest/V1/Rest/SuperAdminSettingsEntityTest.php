<?php

namespace SaTest\V1\Rest;

use \PHPUnit_Framework_TestCase as TestCase;
use Sa\V1\Rest\SuperAdminSettings\SuperAdminSettingsEntity;
use ZF\Hal\Link\LinkCollection;

/**
 * Class SuperAdminSettingsEntityTest
 * @package SaTest\V1\Rest
 */
class SuperAdminSettingsEntityTest extends TestCase
{
    /**
     * @var SuperAdminSettingsEntity
     */
    protected $entity;

    /**
     * @before
     */
    protected function setUpEntity()
    {
        $this->entity = new SuperAdminSettingsEntity();
    }

    /**
     * @test
     */
    public function testItShouldCorrectlyAddLinks()
    {
        /**@var LinkCollection $links*/
        $links = $this->entity->getLinks();
        $this->assertInstanceOf(LinkCollection::class, $links);
        $this->assertTrue($links->has('user'));
        $this->assertTrue($links->has('games'));
        $this->assertTrue($links->has('game-data'));
        $this->assertTrue($links->has('group'));
        $this->assertTrue($links->has('org'));
        $this->assertTrue($links->has('flip'));

        $this->assertEquals($links->count(), 6);
    }
}
