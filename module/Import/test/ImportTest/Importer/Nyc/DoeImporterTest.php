<?php

namespace ImportTest\Importer\Nyc;

use Import\Importer\Nyc\DoeImporter;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Class NycDoeImporterTest
 * @package ImportTest\Importer
 */
class DoeImporterTest extends TestCase
{
    public function testItShouldReturnErrorsOnBadDdbnnn()
    {
        $this->markTestIncomplete('Not done with parsers');
        $importer = new DoeImporter();
        $importer->setFile(__DIR__ . '/_files/bad_ddbnnn.xlsx');

        $this->assertTrue($importer->canImport(), "Adjust test for new NYC DOE requirements");

        $importer->preProcess();

        $expectedErrors = [
            'Sheet \'Classes\' Row 2 has an invalid DDBNNN number',
            'Sheet \'Teachers\' Row 2 has an invalid DDBNNN number',
            'Sheet \'Teachers\' Row 4 has an invalid DDBNNN number',
            'Sheet \'Students\' Row 3 has an invalid DDBNNN number',
        ];

        $this->assertEquals($expectedErrors, $importer->getErrors());
    }
}
