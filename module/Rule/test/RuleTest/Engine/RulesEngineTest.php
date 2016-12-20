<?php

namespace RuleTest\Engine;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\CallbackAction;
use Rule\Basic\AlwaysSatisfiedRule;
use Rule\Engine\Engine;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\Specification\SpecificationCollection;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Test RulesEngineTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RulesEngineTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldAttachToEvents()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $called    = false;
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->andThrow(new ServiceNotFoundException())->byDefault();
        $container->shouldReceive('has')->andReturn(false)->byDefault();

        $spec = [
            'id'      => 'foo-bar',
            'name'    => 'This is a test that the foo will bar',
            'when'    => 'some.event',
            'rules'   => [
                [
                    'rule' => ['name' => AlwaysSatisfiedRule::class],
                ],
            ],
            'actions' => [
                new CallbackAction(function () use (&$called) {
                    $called = true;
                }),
            ],
        ];

        $collection = new SpecificationCollection();
        $collection->append(new ArraySpecification($spec));

        $sharedEvents = new SharedEventManager();
        $events = new EventManager($sharedEvents);

        $engine = new Engine(
            $sharedEvents,
            $container,
            $collection
        );

        $event = new Event();
        $event->setName('some.event');
        $events->triggerEvent($event);

        $this->assertTrue(
            $called,
            'It appears that the Engine did nothing'
        );
    }
}
