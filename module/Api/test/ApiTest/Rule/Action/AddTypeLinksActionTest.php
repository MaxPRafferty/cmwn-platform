<?php

namespace ApiTest\Rule\Action;

use Api\Links\GroupLink;
use Api\Rule\Action\AddTypeLinksAction;
use Api\V1\Rest\User\UserEntity;
use Application\Utils\StaticType;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Rule\Item\RuleItemInterface;

/**
 * Class AddTypeLinksActionTest
 * @package ApiTest\Rule\Action
 */
class AddTypeLinksActionTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var RuleItemInterface
     */
    protected $item;

    /**
     * @before
     */
    public function setUpProviders()
    {
        $this->item = \Mockery::mock(RuleItemInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldAddTypeLinks()
    {
        $this->item->shouldReceive('getParam')
            ->with('types')
            ->andReturn(['class', 'school']);
        $entity = new UserEntity();
        $this->item->shouldReceive('getParam')
            ->with('provider')
            ->andReturn($entity)
            ->once();

        StaticType::setTypes(['class' => 'group_class', 'school' => 'group_school']);

        $this->assertFalse($entity->getLinks()->has('group_class'));
        $this->assertFalse($entity->getLinks()->has('group_school'));

        $action = new AddTypeLinksAction(GroupLink::class, 'types', 'provider');
        $action($this->item);

        $this->assertTrue($entity->getLinks()->has('group_class'));
        $this->assertTrue($entity->getLinks()->has('group_school'));
    }

    /**
     * @test
     */
    public function testItShouldNotAddLinksIfEntityIsNotLinksAware()
    {
        $this->item->shouldReceive('getParam')
            ->with('types')
            ->andReturn(['class', 'school']);
        $entity = new UserEntity();
        $this->item->shouldReceive('getParam')
            ->with('provider')
            ->andReturn($entity->getArrayCopy())
            ->once();
        StaticType::setTypes(['class' => 'group_class', 'school' => 'group_school']);

        $this->assertFalse($entity->getLinks()->has('group_class'));
        $this->assertFalse($entity->getLinks()->has('group_school'));

        $action = new AddTypeLinksAction(GroupLink::class, 'types', 'provider');
        $action($this->item);

        $this->assertFalse($entity->getLinks()->has('group_class'));
        $this->assertFalse($entity->getLinks()->has('group_school'));
    }
}
