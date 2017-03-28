<?php


namespace FriendTest\Rule\Rule;

use Friend\Friend;
use Friend\FriendInterface;
use Friend\Rule\Rule\FriendStatusEqualsRule;
use PHPUnit\Framework\TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;

/**
 * Unit tests for friend status equals rule
 */
class FriendStatusEqualsRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedIfStatusMatchesDefaultStatus()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'friend',
            new Friend(['friend_status' => FriendInterface::FRIEND])
        ));

        $rule = new FriendStatusEqualsRule();

        $this->assertTrue($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedIfStatusMatchesGivenStatus()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'friend',
            new Friend(['friend_status' => FriendInterface::NOT_FRIENDS])
        ));

        $rule = new FriendStatusEqualsRule('friend', FriendInterface::NOT_FRIENDS);

        $this->assertTrue($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedIfStatusDoesntMatch()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'friend',
            new Friend(['friend_status' => FriendInterface::NOT_FRIENDS])
        ));

        $rule = new FriendStatusEqualsRule();

        $this->assertFalse($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionIfSkribbleIsInvalid()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'skribble',
            new Child(['user_id' => 'foo'])
        ));

        $this->expectException(InvalidProviderType::class);

        $rule = new FriendStatusEqualsRule();

        $rule->isSatisfiedBy($item);
    }
}
