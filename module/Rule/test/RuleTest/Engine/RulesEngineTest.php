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
use Zend\ServiceManager\ServiceManager;

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
        $called    = false;
        $container = new ServiceManager();
        $spec      = [
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
        $events       = new EventManager();
        $events->setSharedManager($sharedEvents);
        new Engine(
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

    /**
     * @test
     */
    public function testItShouldPerformWell()
    {
        $calledTimes = 0;
        $container   = new ServiceManager();
        $collection  = new SpecificationCollection();
        foreach (range(1, 10000) as $specCount) {
            $collection->append(new ArraySpecification([
                'id'      => 'foo-bar-' . $specCount,
                'name'    => 'This is a test that the foo will bar',
                'when'    => 'some.event.' . $specCount,
                'rules'   => [
                    [
                        'rule' => ['name' => AlwaysSatisfiedRule::class],
                    ],
                ],
                'actions' => [
                    new CallbackAction(function () use (&$calledTimes) {
                        $calledTimes++;
                    }),
                ],
            ]));
        }

        $sharedEvents = new SharedEventManager();
        $events       = new EventManager();
        $events->setSharedManager($sharedEvents);
        new Engine(
            $sharedEvents,
            $container,
            $collection
        );

        $event = new Event();
        $event->setName('some.event.5');
        $events->triggerEvent($event);

        $this->assertEquals(
            1,
            $calledTimes,
            'It appears that the Engine did nothing'
        );
    }
}
