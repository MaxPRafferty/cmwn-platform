<?php

namespace RuleTest\Engine;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\CallbackAction;
use Rule\Basic\AlwaysSatisfiedRule;
use Rule\Basic\NeverSatisfiedRule;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\EngineHandler;
use Zend\EventManager\Event;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Test EngineHandlerTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EngineHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldExecuteActionsWhenRulesAreHappy()
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

        $arraySpec = new ArraySpecification($spec);
        $handler   = new EngineHandler($container, $arraySpec);
        $handler(new Event('some.event'));

        $this->assertTrue(
            $called,
            'It appears that the Engine Handler did nothing'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotExecuteActionsWhenRulesAreSad()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $called    = false;
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->andThrow(new ServiceNotFoundException())->byDefault();
        $container->shouldReceive('has')->andReturn(false)->byDefault();

        $spec = [
            'id'      => 'foo-bar',
            'name'    => 'This is a test that the foo will never bar',
            'when'    => 'some.event',
            'rules'   => [
                [
                    'rule' => ['name' => NeverSatisfiedRule::class],
                ],
            ],
            'actions' => [
                new CallbackAction(function () use (&$called) {
                    $called = true;
                }),
            ],
        ];

        $arraySpec = new ArraySpecification($spec);
        $handler   = new EngineHandler($container, $arraySpec);
        $handler(new Event('some.event'));

        $this->assertFalse(
            $called,
            'It appears that the Engine Handler executed actions when it was\'t supposed too'
        );
    }
}
