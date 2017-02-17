<?php

namespace ApiTest\Rule\Provider;

use Api\Rule\Provider\ActiveUserOrgRoleProvider;
use Api\V1\Rest\Org\OrgEntity;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\SecurityUser;
use Security\Service\SecurityOrgServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class ActiveUserOrgRoleProviderTest
 * @package ApiTest\Rule\Provider
 */
class ActiveUserOrgRoleProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AuthenticationServiceInterface | \Mockery\MockInterface
     */
    protected $authService;

    /**
     * @var SecurityOrgServiceInterface | \Mockery\MockInterface
     */
    protected $securityOrgService;

    /**
     * @var ActiveUserOrgRoleProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpProvider()
    {
        $this->authService = \Mockery::mock(AuthenticationServiceInterface::class);
        $this->securityOrgService = \Mockery::mock(SecurityOrgServiceInterface::class);
        $this->provider = new ActiveUserOrgRoleProvider($this->authService, $this->securityOrgService);
        $event = new Event();
        $event->setParam('entity', new Entity(new OrgEntity()));
        $this->provider->setEvent($event);
    }

    /**
     * @test
     */
    public function testItShouldReturnGuestIfEntityIsNonOrgInstance()
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
    public function testItShouldReturnRoleOfUserInAnOrg()
    {
        $authUser = new SecurityUser(['user_id' => 'foo']);
        $this->authService->shouldReceive('getIdentity')
            ->andReturn($authUser)
            ->once();
        $this->securityOrgService->shouldReceive('getRoleForOrg')
            ->with($this->provider->getEvent()->getParam('entity')->getEntity(), $authUser)
            ->andReturn('principal.adult')
            ->once();

        $this->assertEquals('principal.adult', $this->provider->getValue());
    }
}
