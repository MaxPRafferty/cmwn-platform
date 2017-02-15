<?php

namespace ApplicationTest\Utils\Date;

use Application\Utils\Date\ToDateFilter;
use PHPUnit\Framework\TestCase;

/**
 * Test ToDateFilterTest
 *
 * @group Filter
 * @group Date
 * @group U
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ToDateFilterTest extends TestCase
{
    /**
     * @test
     * @dataProvider emptyDateValues
     */
    public function testItShouldConvertEmptyValuesToToday($value)
    {
        $expected = new \DateTime('now');
        $filter   = new ToDateFilter();

        $expected->setTime(0, 0, 0);
        $actual = $filter->filter($value);
        $this->assertInstanceOf(\DateTime::class, $actual);
        $actual->setTime(0, 0, 0);

        $this->assertEquals(
            $expected->getTimestamp(),
            $actual->getTimestamp(),
            'Date filter did not set to today'
        );
    }

    /**
     * @test
     */
    public function testItShouldKeepDateTimeTheSame()
    {
        $expected = new \DateTime('now');
        $filter   = new ToDateFilter();

        $actual = $filter->filter($expected);

        $this->assertSame(
            $expected,
            $actual,
            'Date filter changed the object being passed in'
        );
    }

    /**
     * @test
     * @dataProvider emptyDateValues
     */
    public function testItShouldUseDifferentStartDate($value)
    {
        $expected = new \DateTime('tomorrow');
        $filter   = new ToDateFilter(['default_start_date' => 'tomorrow']);

        $expected->setTime(0, 0, 0);
        $actual = $filter->filter($value);
        $this->assertInstanceOf(\DateTime::class, $actual);
        $actual->setTime(0, 0, 0);

        $this->assertEquals(
            $expected->getTimestamp(),
            $actual->getTimestamp(),
            'Date filter did not set to tomorrow'
        );
    }

    /**
     * @return array
     */
    public function emptyDateValues()
    {
        return [
            'Null'         => [null],
            'Zero Int'     => [0],
            'Zero Float'   => [0.0],
            'Empty String' => [''],
            'False'        => [false],
        ];
    }
}
