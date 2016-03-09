<?php

namespace ImportTest\Importer;

use Import\Importer\NycDoeImporter;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Class NycDoeImporterTest
 * @package ImportTest\Importer
 */
class NycDoeImporterTest extends TestCase
{
    public function testItShouldReturnErrorsOnBadDdbnnn()
    {
        $importer = new NycDoeImporter();
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
