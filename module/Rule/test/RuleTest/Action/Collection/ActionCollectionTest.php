<?php

namespace RuleTest\Action\Collection;

use PHPUnit\Framework\TestCase;
use Rule\Action\Collection\ActionCollection;
use Rule\Action\CallbackAction;
use Rule\Item\BasicRuleItem;

/**
 * Test ActionCollectionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActionCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldIterateOverActions()
    {
        $collection = new ActionCollection();

        $action = new CallbackAction(function () {
        });

        $collection->append($action);

        foreach ($collection as $actualAction) {
            $this->assertSame(
                $actualAction,
                $action,
                'Collection did not iterate over actions'
            );
        }
    }

    /**
     * @test
     */
    public function testItShouldExecuteAllActions()
    {
        $calledActions = 0;
        $collection    = new ActionCollection();

        $actionOne = new CallbackAction(function () use (&$calledActions) {
            $calledActions++;
        });

        $actionTwo = new CallbackAction(function () use (&$calledActions) {
            $calledActions++;
        });

        $collection->append($actionOne)->append($actionTwo);
        $item = new BasicRuleItem();

        $collection->__invoke($item);

        $this->assertEquals(
            2,
            $calledActions,
            'Action Collection did not execute actions'
        );
    }
}
