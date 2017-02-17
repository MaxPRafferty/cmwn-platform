<?php

namespace ApiTest\Rule\Provider;

use Api\Rule\Provider\ActiveUserGroupRoleProvider;
use Api\V1\Rest\Group\GroupEntity;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\SecurityUser;
use Security\Service\SecurityGroupServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class ActiverUserGroupRoleProviderTest
 */
class ActiverUserGroupRoleProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AuthenticationServiceInterface | \Mockery\MockInterface
     */
    protected $authService;

    /**
     * @var SecurityGroupServiceInterface | \Mockery\MockInterface
     */
    protected $securityGroupService;

    /**
     * @var ActiveUserGroupRoleProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpProvider()
    {
        $this->authService = \Mockery::mock(AuthenticationServiceInterface::class);
        $this->securityGroupService = \Mockery::mock(SecurityGroupServiceInterface::class);
        $this->provider = new ActiveUserGroupRoleProvider($this->authService, $this->securityGroupService);
        $event = new Event();
        $event->setParam('entity', new Entity(new GroupEntity()));
        $this->provider->setEvent($event);
    }

    /**
     * @test
     */
    public function testItShouldReturnGuestIfEntityIsNonGroupInstance()
    {
        $this->provider->getEvent()->setParam('entity', new UserEntity());
        $this->assertEquals('guest', $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnSuperIfAuthUserIsSuper()
    {
        $this->authService->shouldReceive('getIdentity')
           ->andReturn(new SecurityUser(['user_id' => 'foo', 'super' => 1]));
        $this->assertEquals('super', $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnRoleOfUserInAGroup()
    {
        $authUser = new SecurityUser(['user_id' => 'foo']);
        $this->authService->shouldReceive('getIdentity')
            ->andReturn($authUser)
            ->once();
        $this->securityGroupService->shouldReceive('getRoleForGroup')
            ->with($this->provider->getEvent()->getParam('entity')->getEntity(), $authUser)
            ->andReturn('principal.adult')
            ->once();

        $this->assertEquals('principal.adult', $this->provider->getValue());
    }
}
