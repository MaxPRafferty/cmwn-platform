<?php

namespace RuleTest\Action;

use PHPUnit\Framework\TestCase as TestCase;
use Rule\Action\CallbackAction;
use Rule\Item\BasicRuleItem;

/**
 * Test CallbackActionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CallbackActionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCallFunctionOnInvoke()
    {
        $called = false;
        $ruleItem = new BasicRuleItem();
        $action = new CallbackAction(function ($item) use (&$called, &$ruleItem) {
            $this->assertSame(
                $ruleItem,
                $item,
                'Rule Action did not pass Rule Item to the action'
            );
            $called = true;
        });

        $action->__invoke($ruleItem);
        $this->assertTrue(
            $called,
            'CallbackAction did not call action'
        );
    }
}
