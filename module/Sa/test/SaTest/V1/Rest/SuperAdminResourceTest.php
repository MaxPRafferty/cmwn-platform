<?php

namespace SaTest\V1\Rest;

use \PHPUnit_Framework_TestCase as TestCase;
use Sa\V1\Rest\SuperAdminSettings\SuperAdminSettingsEntity;
use Sa\V1\Rest\SuperAdminSettings\SuperAdminSettingsResource;

/**
 * Class SuperAdminResourceTest
 * @package SaTest\V1\Rest
 */
class SuperAdminResourceTest extends TestCase
{

    /**
     * @var SuperAdminSettingsResource
     */
    protected $resource;

    /**
     * @before
     */
    public function setUpResource()
    {
        $this->resource = new SuperAdminSettingsResource();
    }

    /**
     * @test
     */
    public function testItShouldReceiveFetchAllAndReturnSaSettingsEntity()
    {
        $this->assertEquals($this->resource->fetchAll(), new SuperAdminSettingsEntity([]));
    }
}
