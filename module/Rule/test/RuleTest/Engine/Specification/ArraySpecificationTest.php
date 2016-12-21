<?php

namespace RuleTest\Engine\Specification;

use Interop\Container\ContainerInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Action\ActionCollectionInterface;
use Rule\Action\NoopAction;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Rule\Basic\NeverSatisfiedRule;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Rule\Collection\RuleCollectionInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

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
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $container->shouldReceive('get')->andThrow(new ServiceNotFoundException())->byDefault();
        $container->shouldReceive('has')->andReturn(false)->byDefault();

        $spec = [
            'id'          => 'foo-bar',
            'name'        => 'This is a test that the foo will bar',
            'when'        => 'some.event',
            'rules'       => [
                [
                    'rule' => ['name' => AlwaysSatisfiedRule::class],
                ],
            ],
            'actions'     => [
                ['name' => NoopAction::class],
            ],
            'item_params' => [
                'active_user' => 'MANCHUCK',
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
            $spec['when'],
            $arraySpec->getEventName(),
            'The Event name does not match the expected spec'
        );

        $item = $arraySpec->buildItem($container);
        $this->assertEquals(
            'MANCHUCK',
            $item->getParam('active_user'),
            'The item that was built does not have the correct parameter'
        );

        $rules = $arraySpec->getRules($container);
        $this->assertInstanceOf(
            RuleCollectionInterface::class,
            $rules,
            'Rules built are not a Collection of rules'
        );

        $this->assertTrue(
            $rules->isSatisfiedBy($item),
            'Spec for this array should be satisfied'
        );

        $actions = $arraySpec->getActions($container);
        $this->assertInstanceOf(
            ActionCollectionInterface::class,
            $actions,
            'Actions built are not a Collection of actions'
        );
    }

    /**
     * @test
     */
    public function testItShouldBuildARuleSetWithNot()
    {
        /** @var \Mockery\MockInterface|ContainerInterface $container */
        $container = \Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->andThrow(new ServiceNotFoundException())->byDefault();
        $container->shouldReceive('has')->andReturn(false)->byDefault();

        $spec = [
            'id'          => 'foo-bar',
            'name'        => 'This is a test that the foo will bar',
            'when'        => 'some.event',
            'rules'       => [
                [
                    'rule' => [
                        'name'     => NeverSatisfiedRule::class,
                    ],
                    'operator' => 'not',
                ],
            ],
            'actions'     => [
                ['name' => NoopAction::class],
            ],
            'item_params' => [],
        ];

        $arraySpec = new ArraySpecification($spec);

        $rules = $arraySpec->getRules($container);
        $this->assertTrue(
            $rules->isSatisfiedBy($arraySpec->buildItem($container)),
            'This rule set should be satisfied since the "not" operator was specified'
        );
    }
}
