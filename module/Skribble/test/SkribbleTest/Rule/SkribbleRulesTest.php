<?php

namespace SkribbleTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Skribble\Rule\SkribbleRules;
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
    /**
     * @test
     */
    public function testItShouldHydrateCorrectlyFromJson()
    {
        $json          = file_get_contents(__DIR__ . '/_files/valid.skribble.json');
        $skribbleData  = Json::decode($json, Json::TYPE_ARRAY);
        $skribbleRules = new SkribbleRules($skribbleData['rules']);

        $this->assertEquals($skribbleData['rules'], $skribbleRules->getArrayCopy());
    }
}
