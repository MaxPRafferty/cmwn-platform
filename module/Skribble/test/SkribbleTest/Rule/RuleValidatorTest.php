<?php

namespace SkribbleTest\Rule;

use PHPUnit\Framework\TestCase;
use Skribble\Rule\RuleValidator;
use Skribble\Rule\SkribbleRules;
use Zend\Json\Json;

/**
 * Test RuleValidatorTest
 *
 * @group Skribble
 * @group Validator
 * @group SkribbleRule
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RuleValidatorTest extends TestCase
{
    /**
     * @before
     */
    public function setUp()
    {
        $this->markTestIncomplete('Rules are not updated yet ');
    }

    /**
     * @test
     */
    public function testItShouldPassValidRules()
    {
        $json          = file_get_contents(__DIR__ . '/../_files/valid.skribble.json');
        $skribbleData  = Json::decode($json, Json::TYPE_ARRAY);

        $validator = new RuleValidator();
        $this->assertTrue($validator->isValid($skribbleData['rules']), 'Validator did not validate correct rules');
    }

    /**
     * @test
     */
    public function testItShouldPassWithSkribbleRules()
    {
        $validator = new RuleValidator();
        $this->assertTrue(
            $validator->isValid(new SkribbleRules()),
            'Validator did not pass with a SkribbleRules object'
        );
    }

    /**
     * @test
     * @dataProvider badTypeProvider
     */
    public function testItShouldFailWithNonArrayPassed($badType)
    {
        $validator = new RuleValidator();
        $this->assertFalse(
            $validator->isValid($badType),
            'Validator did not fail with an invalid type'
        );

        $this->assertEquals(
            ['invalidType' => 'Rules must be an array or object'],
            $validator->getMessages(),
            'Validator is reporting incorrect messages'
        );
    }

    /**
     * @test
     * @dataProvider overFlowProvider
     * TODO Test when rules are being validated
     */
    public function testItShouldFailWithOverflowException()
    {
        $this->markTestIncomplete('Need to improve rules validating');
        $validator = new RuleValidator();
    }

    public function overFlowProvider()
    {
        return [
            'Too many backgrounds' => [

            ]
        ];
    }

    /**
     * @return array
     */
    public function badTypeProvider()
    {
        return [
            'Integer' => [
                'badType' => 1,
            ],

            'String' => [
                'badType' => 'foo-bar',
            ],

            'Float' => [
                'badType' => 0.0,
            ],

            'Bad Object' => [
                'badType' => new \stdClass(),
            ],
        ];
    }
}
