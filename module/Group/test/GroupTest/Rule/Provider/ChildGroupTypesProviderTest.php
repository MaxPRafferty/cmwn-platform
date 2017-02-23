<?php

namespace GroupTest\Rule\Provider;

use Api\V1\Rest\Group\GroupEntity;
use Api\V1\Rest\User\UserEntity;
use Group\Rule\Provider\ChildGroupTypesProvider;
use Group\Service\GroupServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Unit tests for child group type provider
 */
class ChildGroupTypesProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var ChildGroupTypesProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = \Mockery::mock(GroupServiceInterface::class);
        $this->provider = new ChildGroupTypesProvider($this->groupService);
        $event = new Event();
        $entity = new Entity(new GroupEntity());
        $event->setParam('entity', $entity);
        $this->provider->setEvent($event);
    }

    /**
     * @test
     */
    public function testItShouldReturnChildGroupTypes()
    {
        $types = ['class'];
        $this->groupService->shouldReceive('fetchChildTypes')
            ->andReturn(['class'])
            ->once();
        $this->assertEquals($types, $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnEmptyArrayIfEntityIsNonGroup()
    {
        $event = new Event();
        $event->setParam('entity', new Entity(new UserEntity()));
        $this->provider->setEvent($event);
        $types = [];
        $this->groupService->shouldReceive('fetchChildTypes')
            ->never();
        $this->assertEquals($types, $this->provider->getValue());
    }
}
