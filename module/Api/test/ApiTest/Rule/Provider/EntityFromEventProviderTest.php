<?php

namespace ApiTest\Rule\Provider;

use Api\Rule\Provider\RealEntityFromEventProvider;
use Api\V1\Rest\User\UserEntity;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Zend\EventManager\Event;
use ZF\Hal\Entity;

/**
 * Class EntityFromEventProviderTest
 *
 * @package ApiTest\Rule\Provider
 */
class EntityFromEventProviderTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var RealEntityFromEventProvider
     */
    protected $provider;

    /**
     * @before
     */
    public function setUpProvider()
    {
        $this->provider = new RealEntityFromEventProvider();
    }

    /**
     * @test
     */
    public function testItShouldReturnEntityFromAnEvent()
    {
        $userEntity = new UserEntity();
        $event      = new Event();
        $event->setParam('entity', new Entity($userEntity));
        $this->provider->setEvent($event);
        $this->assertEquals($userEntity, $this->provider->getValue());
    }

    /**
     * @test
     */
    public function testItShouldReturnNullIfEntityIsNotEntityOrLinksAware()
    {
        $userEntity = new UserEntity();
        $event      = new Event();
        $event->setParam('entity', $userEntity->getArrayCopy());
        $this->provider->setEvent($event);
        $this->assertNull($this->provider->getValue());
    }
}
