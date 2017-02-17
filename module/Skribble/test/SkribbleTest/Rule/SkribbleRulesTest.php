<?php

namespace SkribbleTest\Rule;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Skribble\Rule\Background;
use Skribble\Rule\Effect;
use Skribble\Rule\Item;
use Skribble\Rule\RuleCompositeInterface;
use Skribble\Rule\SkribbleRules;
use Skribble\Rule\Sound;
use Zend\Json\Json;

/**
 * Test SkribbleRulesTest
 *
 * @Skribble
 * @SkribbleRules
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SkribbleRulesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     */
    public function testItShouldHydrateCorrectlyFromJson()
    {
        $json          = file_get_contents(__DIR__ . '/../_files/valid.skribble.json');
        $skribbleData  = Json::decode($json, Json::TYPE_ARRAY);
        $skribbleRules = new SkribbleRules($skribbleData['rules']);

        $this->assertEquals($skribbleData['rules'], $skribbleRules->getArrayCopy());
        $this->assertTrue($skribbleRules->isValid(), 'Skribble rules MUST be valid');
        $this->assertEquals('rules', $skribbleRules->getRuleType());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenBackgroundIsInvalid()
    {
        $this->expectException(\Skribble\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule is not a Background');

        $item  = new Item();
        $rules = new SkribbleRules();
        $rules->setBackground($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenSoundIsInvalid()
    {
        $this->expectException(\Skribble\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule is not a Sound');

        $item  = new Item();
        $rules = new SkribbleRules();
        $rules->setSound($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenEffectIsInvalid()
    {
        $this->expectException(\Skribble\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule is not an Effect');

        $item  = new Item();
        $rules = new SkribbleRules();
        $rules->setEffect($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenItemIsInvalid()
    {
        $this->expectException(\Skribble\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule is not an Item');

        $item  = new Sound();
        $rules = new SkribbleRules();
        $rules->addItem($item);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenMessageIsInvalid()
    {
        $this->expectException(\Skribble\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule is not a Message');

        $item  = new Item();
        $rules = new SkribbleRules();
        $rules->addMessage($item);
    }

    /**
     * @test
     * @dataProvider badRuleArrayProvider
     */
    public function testItShouldThrowExceptionWithBadArrayType($method, $ruleData, $message)
    {
        $this->expectException(\Skribble\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $rules = new SkribbleRules();
        $rules->{$method}($ruleData);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenRuleIsNotAllowed()
    {
        /** @var \Mockery\MockInterface|\Skribble\Rule\RuleCompositeInterface $rule */
        $rule = \Mockery::mock('\Skribble\Rule\RuleCompositeInterface');
        $rule->shouldReceive('getRuleType')
            ->andReturn('FooBar')
            ->byDefault();

        $this->expectException(\Skribble\UnexpectedValueException::class);
        $this->expectExceptionMessage('Rule of type "FooBar" is currently not supported');

        $skribbleRules = new SkribbleRules();
        $skribbleRules->addRule($rule);
    }

    /**
     * @test
     *
     * @param $rule
     *
     * @dataProvider restrictedRulesDataProvider
     */
    public function testItShouldNotAllowMultipleRulesBasedOnRestrictedType(RuleCompositeInterface $rule)
    {
        $this->expectException(\Skribble\OverflowException::class);
        $this->expectExceptionMessage(sprintf('Only one rule of type "%s" can be set', $rule->getRuleType()));

        $skribbleRules = new SkribbleRules();
        $skribbleRules->addRule($rule);

        $new = clone $rule;
        $skribbleRules->addRule($new);
    }

    /**
     * @return array
     */
    public function badRuleArrayProvider()
    {
        return include __DIR__ . '/_files/bad.rule.provider.php';
    }

    /**
     * @return array
     */
    public function restrictedRulesDataProvider()
    {
        return [
            'Background' => [
                'rule' => new Background(),
            ],

            'Sound' => [
                'rule' => new Sound(),
            ],

            'Effect' => [
                'rule' => new Effect(),
            ],
        ];
    }
}
