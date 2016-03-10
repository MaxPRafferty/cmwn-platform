<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception ClassWorksheetParserTest
 */
class ClassWorksheetParserTest extends TestCase
{
    public function testItShouldErrorWithInvalidDdbnnn()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_invalid_ddbnnn.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = new ClassWorksheetParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Classes" Row: 2 Invalid DDBNNN 144Q1001'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors with a bad DDBNNN number'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a invalid DDBNNN number was found'
        );

        $this->assertFalse(
            $parser->hasWarnings(),
            'Parser is reporting warnings when this file should not have any'
        );

        $this->assertSame(
            [],
            $parser->getWarnings(),
            'Parser reported warnings that It should not have'
        );
    }

    public function testItShouldErrorWhenClassMissingTitle()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_missing_title.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = new ClassWorksheetParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Classes" Row: 2 Missing class title'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a class is missing a title'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a class was missing a title'
        );

        $this->assertFalse(
            $parser->hasWarnings(),
            'Parser is reporting warnings when this file should not have any'
        );

        $this->assertSame(
            [],
            $parser->getWarnings(),
            'Parser reported warnings that It should not have'
        );
    }

    public function testItShouldErrorWhenClassMissingId()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_missing_class_id.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = new ClassWorksheetParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Classes" Row: 2 Missing class id'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a class is missing an id'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a class was missing an id'
        );

        $this->assertFalse(
            $parser->hasWarnings(),
            'Parser is reporting warnings when this file should not have any'
        );

        $this->assertSame(
            [],
            $parser->getWarnings(),
            'Parser reported warnings that It should not have'
        );
    }
}
