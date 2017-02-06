<?php

namespace OrgTest\Rule\Provider;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Org\Rule\Provider\OrgTypesProvider;
use Org\Service\OrganizationServiceInterface;

/**
 * Class OrgTypesProviderTest
 * @package OrgTest\Rule\Provider
 */
class OrgTypesProviderTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * @var
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->orgService = \Mockery::mock(OrganizationServiceInterface::class);
        $this->provider = new OrgTypesProvider($this->orgService);
    }

    /**
     * @test
     */
    public function testItShouldReturnAllGroupTypes()
    {
        $types = ['district'];
        $this->orgService->shouldReceive('fetchOrgTypes')
            ->andReturn(['district'])
            ->once();
        $this->assertEquals($types, $this->provider->getValue());
    }
}
