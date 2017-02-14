<?php

namespace RuleTest\Engine\Specification;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Action\NoopAction;
use Rule\Rule\Basic\AlwaysSatisfiedRule;
use Rule\Engine\Specification\ArraySpecification;
use Rule\Engine\Specification\SpecificationCollection;
use Rule\Exception\RuntimeException;

/**
 * Test SpecificationCollectionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SpecificationCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldAddAllSpecifications()
    {
        $specOne = new ArraySpecification([
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
        ]);

        $specTwo = new ArraySpecification([
            'id'          => 'baz-bat',
            'name'        => 'This is a test that the baz will bat',
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
        ]);

        $collection = new SpecificationCollection();
        $collection->append($specOne)->append($specTwo);

        $iterator = $collection->getIterator();
        $iterator->rewind();
        $this->assertSame(
            $specOne,
            $iterator->current(),
            'Spec one is not in the collection'
        );

        $iterator->next();
        $this->assertSame(
            $specTwo,
            $iterator->current(),
            'Spec one is not in the collection'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenAppendingDuplicateSpecification()
    {
        $specOne = new ArraySpecification([
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
        ]);

        $specTwo = new ArraySpecification([
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
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('A specification with the id foo-bar already exists');
        $collection = new SpecificationCollection();
        $collection->append($specOne)->append($specTwo);
    }
}
