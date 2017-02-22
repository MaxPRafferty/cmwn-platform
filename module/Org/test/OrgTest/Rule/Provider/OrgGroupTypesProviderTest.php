<?php


namespace OrgTest\Rule\Provider;

use Api\V1\Rest\Org\OrgEntity;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Org\Rule\Provider\OrgGroupTypesProvider;
use Org\Service\OrganizationServiceInterface;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class OrgGroupTypesProviderTest
 * @package OrgTest\Rule\Provider
 */
class OrgGroupTypesProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface | OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * @var OrgGroupTypesProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->orgService = \Mockery::mock(OrganizationServiceInterface::class);
        $this->provider = new OrgGroupTypesProvider($this->orgService);
        $event = new Event();
        $entity = new Entity(new OrgEntity());
        $event->setParam('entity', $entity);
        $this->provider->setEvent($event);
    }

    /**
     * @test
     */
    public function testItShouldReturnGroupTypesForOrg()
    {
        $types = ['class'];
        $this->orgService->shouldReceive('fetchGroupTypes')
            ->with($this->provider->getEvent()->getParam('entity')->getEntity())
            ->andReturn(['class'])
            ->once();
        $this->assertEquals($types, $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnEmptyArrayIfEntityIsNonOrg()
    {
        $event = new Event();
        $event->setParam('entity', new Entity(new UserEntity()));
        $this->provider->setEvent($event);
        $types = [];
        $this->orgService->shouldReceive('fetchGroupTypes')
            ->andReturn([])
            ->never();
        $this->assertEquals($types, $this->provider->getValue());
    }
}
