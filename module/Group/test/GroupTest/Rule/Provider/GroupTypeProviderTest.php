<?php

namespace GroupTest\Rule\Provider;

use Group\Rule\Provider\GroupTypesProvider;
use Group\Service\GroupServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Unit tests for group type provider
 */
class GroupTypeProviderTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = \Mockery::mock(GroupServiceInterface::class);
        $this->provider = new GroupTypesProvider($this->groupService);
    }

    /**
     * @test
     */
    public function testItShouldReturnAllGroupTypes()
    {
        $types = ['school', 'class'];
        $this->groupService->shouldReceive('fetchGroupTypes')
            ->andReturn(['school', 'class'])
            ->once();
        $this->assertEquals($types, $this->provider->getValue());
    }
}
