<?php

namespace RuleTest\Engine;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\NoopAction;
use Rule\Basic\AlwaysSatisfiedRule;
use Rule\Engine\ArraySpecification;
use Rule\Provider\BasicValueProvider;
use Rule\RuleCollectionInterface;

/**
 * Test ArraySpecificationTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArraySpecificationTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCreateRulesFromSpec()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->andThrow(new \Exception())->byDefault();

        $spec = [
            'id'          => 'foo-bar',
            'name'        => 'This is a test that the foo will bar',
            'when'        => [
                'identifier' => 'manchuck',
                'event'      => 'attach.friend.post',
            ],
            'rules'       => [
                ['name' => AlwaysSatisfiedRule::class,],
            ],
            'actions'     => [
                ['name' => NoopAction::class],
            ],
            'item_params' => [
                'active_user' => 'MANCHUCK',
                BasicValueProvider::class
            ],
        ];

        $arraySpec = new ArraySpecification($spec);

        $this->assertEquals(
            $spec['id'],
            $arraySpec->getId(),
            'The Id does not match the expected spec'
        );

        $this->assertEquals(
            $spec['name'],
            $arraySpec->getName(),
            'The name does not match the expected spec'
        );

        $this->assertEquals(
            $spec['when']['identifier'],
            $arraySpec->getSharedEventIdentifier(),
            'The Event Id does not match the expected spec'
        );

        $this->assertEquals(
            $spec['when']['event'],
            $arraySpec->getEventName(),
            'The Event name does not match the expected spec'
        );

        $rules = $arraySpec->getRules($container);
        $this->assertInstanceOf(
            RuleCollectionInterface::class,
            $rules,
            'Rules built are not a Collection of rules'
        );

    }
}
