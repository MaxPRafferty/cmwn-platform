<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\Parser\Excel\AbstractExcelParser;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception AbstractParserTest
 *
 * ${CARET}
 */
class AbstractExcelParserTest extends TestCase
{
    public function testItShouldReturnValueObjectWhenValid()
    {
        $dString = '02B001';
        $dValue  = AbstractExcelParser::parseDdbnnn($dString);
        $this->assertInstanceOf('\stdClass', $dValue);

        $this->assertEquals('02', $dValue->district);
        $this->assertEquals('B', $dValue->burough);
        $this->assertEquals('001', $dValue->class);
    }

    /**
     * @dataProvider badDdbnnnProvider
     */
    public function testItShouldThrowExceptionOnBadNumber($dString)
    {
        $this->setExpectedException('Import\Importer\Nyc\Exception\InvalidDdbnnException');
        AbstractExcelParser::parseDdbnnn($dString);
    }

    /**
     * @return array
     */
    public function badDdbnnnProvider()
    {
        return [
            'Too Many digits'         => ['002B2015'],
            'Lowercase left'          => ['02b201'],
            'No Starting number'      => ['B201'],
            'Too few starting num'    => ['2B201'],
            'Too many starting num'   => ['002B201'],
            'No Ending numbers'       => ['02B'],
            'Too Few Ending numbers'  => ['02B00'],
            'Too Many Ending numbers' => ['02B1002'],
            'Truncated numbers'       => ['2B2'],
            'No numbers at all'       => ['DDBNNN'],
        ];
    }
}