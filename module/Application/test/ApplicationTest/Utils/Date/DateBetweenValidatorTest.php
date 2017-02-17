<?php

namespace ApplicationTest\Utils\Date;

use Application\Utils\Date\DateGreaterThanValidator;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test DateBetweenValidatorTest
 *
 * @group Application
 * @group Utils
 * @group Date
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DateBetweenValidatorTest extends TestCase
{
    /**
     * @param $date
     * @param $start
     *
     * @test
     * @dataProvider validDatesProvider
     */
    public function testItShouldValidatePositiveForCorrectDates($date, $start)
    {
        $validator = new DateGreaterThanValidator(['startDate' => $start]);
        $this->assertTrue(
            $validator->isValid($date),
            'Date Validator did not validate correct date'
        );
    }

    /**
     * @param $date
     * @param $start
     *
     * @test
     * @dataProvider invalidDatesProvider
     */
    public function testItShouldValidateNegativeForIncorrectDates($date, $start)
    {
        $validator = new DateGreaterThanValidator(['startDate' => $start]);
        $this->assertFalse(
            $validator->isValid($date),
            'Date Validator did not validate correct date'
        );
    }


    /**
     * @return array
     */
    public function validDatesProvider()
    {
        return [
            'Valid Strings' => [
                'now',
                'yesterday',
            ],
            'Valid Dates' => [
                'May 13, 1982',
                'May 12, 1982',
            ],
            'Valid Time Stamps' => [
                390180960,
                390094560,
            ]
        ];
    }

    /**
     * @return array
     */
    public function invalidDatesProvider()
    {
        return [
            'Valid Strings' => [
                'yesterday',
                'now',
            ],
            'Valid Dates' => [
                'May 12, 1982',
                'May 13, 1982',
            ],
            'Valid Time Stamps' => [
                390094560,
                390180960,
            ]
        ];
    }
}
