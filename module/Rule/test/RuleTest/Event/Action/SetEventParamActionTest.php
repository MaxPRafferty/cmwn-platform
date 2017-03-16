<?php

namespace RuleTest\Event\Action;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Rule\Event\Action\SetEventParamAction;
use Rule\Exception\InvalidProviderType;
use Rule\Item\RuleItemInterface;
use Zend\EventManager\Event;
use \PHPUnit\Framework\TestCase;

/**
 * Class SetEventParamActionTest
 * @package RuleTest\Event\Action
 */
class SetEventParamActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var SetEventParamAction
     */
    protected $action;

    /**
     * @var RuleItemInterface | \Mockery\MockInterface
     */
    protected $item;

    /**
     * @before
     */
    public function setUpAction()
    {
        $this->item = \Mockery::mock(RuleItemInterface::class);
        $this->action = new SetEventParamAction('foo', 'bar', 'event');
    }

    /**
     * @test
     */
    public function testItShouldSetParamToEventIfEventInstanceOfEventInterface()
    {
        $event = new Event();
        $this->item->shouldReceive('getParam')
            ->with('event')
            ->andReturn($event)
            ->once();
        $this->assertFalse($event->getParam('foo', false));
        ($this->action)($this->item);
        $this->assertEquals($event->getParam('foo'), 'bar');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionIfEventNotInstanceOfEventInterface()
    {
        $this->expectException(InvalidProviderType::class);
        $this->item->shouldReceive('getParam')
            ->with('event')
            ->once();

        ($this->action)($this->item);
    }
}
