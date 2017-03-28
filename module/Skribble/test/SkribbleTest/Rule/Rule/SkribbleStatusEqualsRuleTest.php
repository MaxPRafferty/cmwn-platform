<?php

namespace SkribbleTest\Rule\Rule;

use PHPUnit\Framework\TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use Skribble\Rule\Rule\SkribbleStatusEqualsRule;
use Skribble\Skribble;
use Skribble\SkribbleInterface;

/**
 * unit tests for skribble status equals rule
 */
class SkribbleStatusEqualsRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeSatisfiedIfStatusMatchesDefaultStatus()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'skribble',
            new Skribble(['status' => SkribbleInterface::STATUS_COMPLETE])
        ));

        $rule = new SkribbleStatusEqualsRule();

        $this->assertTrue($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedIfStatusMatchesGivenStatus()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'skribble',
            new Skribble(['status' => SkribbleInterface::STATUS_NOT_COMPLETE])
        ));

        $rule = new SkribbleStatusEqualsRule('skribble', SkribbleInterface::STATUS_NOT_COMPLETE);

        $this->assertTrue($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedIfStatusDoesntMatch()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'skribble',
            new Skribble(['status' => SkribbleInterface::STATUS_COMPLETE])
        ));

        $rule = new SkribbleStatusEqualsRule('skribble', SkribbleInterface::STATUS_NOT_COMPLETE);

        $this->assertFalse($rule->isSatisfiedBy($item));
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionIfSkribbleIsInvalid()
    {
        $item = new BasicRuleItem(new BasicValueProvider(
            'skribble',
            null
        ));

        $this->expectException(InvalidProviderType::class);

        $rule = new SkribbleStatusEqualsRule();

        $rule->isSatisfiedBy($item);
    }
}
