<?php

namespace RuleTest\Item;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;

/**
 * Test RuleItemTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BasicRuleItemTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCloneObjects()
    {
        $user = new \stdClass();
        $item = new BasicRuleItem(new BasicValueProvider('active_user', $user));

        $this->assertEquals(
            $user,
            $item->getParam('active_user'),
            'Rule Item did not return an equivalent active user'
        );

        $this->assertNotSame(
            $user,
            $item->getParam('active_user'),
            'Rule Item returned the same user.  ' .
            'This is bad we don\'t want to give rule specifications the ability to change the active user'
        );
    }
}
