<?php

namespace RuleTest\Engine;

use \PHPUnit_Framework_TestCase as TestCase;
use Rule\Date\DateBetweenSpecification;

/**
 * Test RulesEngineTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RulesEngineTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBuildEngine()
    {

        $config = [
            'rules'   => [
                [
                    'name' => DateBetweenSpecification::class,
                    'options' => [
                        'start_date' => new \DateTime(),
                        'end_date'   => new \DateTime(),
                    ]
                ]
            ],
            'actions' => [],
        ];
    }
}
