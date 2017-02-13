<?php

namespace ApiTest\Rule\Provider;

use Api\Rule\Provider\UserRelationshipProvider;
use Api\V1\Rest\Group\GroupEntity;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\SecurityUser;
use Security\Service\SecurityUserServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class UserRelationshipProviderTest
 * @package ApiTest\Rule\Provider
 */
class UserRelationshipProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AuthenticationServiceInterface | \Mockery\MockInterface
     */
    protected $authService;

    /**
     * @var SecurityUserServiceInterface | \Mockery\MockInterface
     */
    protected $securityUserService;

    /**
     * @var UserRelationshipProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpProvider()
    {
        $this->authService = \Mockery::mock(AuthenticationServiceInterface::class);
        $this->securityUserService = \Mockery::mock(SecurityUserServiceInterface::class);
        $this->provider = new UserRelationshipProvider($this->authService, $this->securityUserService);
        $event = new Event();
        $event->setParam('entity', new Entity(new UserEntity()));
        $this->provider->setEvent($event);
    }

    /**
     * @test
     */
    public function testItShouldReturnGuestIfEntityIsNonGroupInstance()
    {
        $this->provider->getEvent()->setParam('entity', new GroupEntity());
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
    public function testItShouldReturnRoleOfUserWithAuthUser()
    {
        $authUser = new SecurityUser(['user_id' => 'foo']);
        $this->authService->shouldReceive('getIdentity')
            ->andReturn($authUser)
            ->once();
        $this->securityUserService->shouldReceive('fetchRelationshipRole')
            ->with($authUser, $this->provider->getEvent()->getParam('entity')->getEntity())
            ->andReturn('principal.adult')
            ->once();

        $this->assertEquals('principal.adult', $this->provider->getValue());
    }
}
