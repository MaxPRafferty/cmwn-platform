<?php

namespace ApiTest\Rule\Provider;

use Api\Rule\Provider\EntityFromEventProvider;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class EntityFromEventProviderTest
 * @package ApiTest\Rule\Provider
 */
class EntityFromEventProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var EntityFromEventProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpProvider()
    {
        $this->provider = new EntityFromEventProvider();
    }

    /**
     * @test
     */
    public function testItShouldReturnEntityFromAnEvent()
    {
        $userEntity = new UserEntity();
        $event = new Event();
        $event->setParam('entity', new Entity($userEntity));
        $this->provider->setEvent($event);
        $this->assertEquals($userEntity, $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnEntityIfEntityIsLinksCollectionAware()
    {
        $userEntity = new UserEntity();
        $event = new Event();
        $event->setParam('entity', $userEntity);
        $this->provider->setEvent($event);
        $this->assertEquals($userEntity, $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnNullIfEntityIsNotEntityOrLinksAware()
    {
        $userEntity = new UserEntity();
        $event = new Event();
        $event->setParam('entity', $userEntity->getArrayCopy());
        $this->provider->setEvent($event);
        $this->assertNull($this->provider->getValue());
    }
}
