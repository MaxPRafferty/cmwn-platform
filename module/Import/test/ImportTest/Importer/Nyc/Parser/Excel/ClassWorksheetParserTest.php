<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Application\Exception\NotFoundException;
use Group\Group;
use Import\Importer\Nyc\ClassRoom\AddClassRoomAction;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use Import\Importer\Nyc\Parser\AbstractParser;
use Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception ClassWorksheetParserTest
 */
class ClassWorksheetParserTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var ClassRoomRegistry
     */
    protected $registry;

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\GroupService');
        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->andThrow(NotFoundException::class)
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpRegistry()
    {
        $this->registry = new ClassRoomRegistry($this->groupService);
    }

    /**
     * @param \PHPExcel_Worksheet $sheet
     * @return ClassWorksheetParser
     */
    protected function getParser(\PHPExcel_Worksheet $sheet)
    {
        AbstractParser::clear();
        return new ClassWorksheetParser($sheet, $this->registry);
    }

    /**
     * @return ClassRoom[]
     */
    protected function getExpectedClassrooms()
    {
        return [
            '001'  => new ClassRoom('Lunch', '001', ['8001', '8002']),
            '102'  => new ClassRoom('PreK', '102', ['8001', '8002']),
            '011'  => new ClassRoom('Kindergarten', '011', ['8001', '8002']),
            '101'  => new ClassRoom('First Grade', '101', ['8001', '8002', '8003']),
            '201'  => new ClassRoom('Second Grade', '201', ['8001', '8002', '8003']),
            '301'  => new ClassRoom('Third Grade', '301', ['8001', '8002', '8003']),
            '8001' => new ClassRoom('Physical Education', '8001', []),
            '8002' => new ClassRoom('Art', '8002', []),
            '8003' => new ClassRoom('Technology', '8003', []),
        ];
    }

    /**
     * @return AddClassRoomAction[]
     */
    protected function getExpectedAddActions()
    {
        $actions = new \SplPriorityQueue();

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Lunch', '001', ['8001', '8002'])
            ),
            100
        );


        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('PreK', '102', ['8001', '8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Kindergarten', '011', ['8001', '8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('First Grade', '101', ['8001', '8002', '8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Second Grade', '201', ['8001', '8002', '8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Third Grade', '301', ['8001', '8002', '8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Physical Education', '8001', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Art', '8002', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Technology', '8003', [])
            ),
            100
        );

        return $actions;
    }
    /**
     * @return AddClassRoomAction[]
     */
    protected function getExpectedMixedActions()
    {
        $actions = new \SplPriorityQueue();

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Lunch', '001', ['8001', '8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('PreK', '102', ['8001', '8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Kindergarten', '011', ['8001', '8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('First Grade', '101', ['8001', '8002', '8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Third Grade', '301', ['8001', '8002', '8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Physical Education', '8001', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Art', '8002', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Technology', '8003', [])
            ),
            100
        );

        return $actions;
    }

    protected function checkRegistry()
    {
        foreach ($this->getExpectedClassrooms() as $classId => $classroom) {
            $this->assertTrue(
                $this->registry->offsetExists($classId),
                sprintf('Parser did not add class room "%s" to registry', $classId)
            );

            $this->assertEquals(
                $classroom,
                $this->registry->offsetGet($classId),
                sprintf('Parser created invalid class room for class "%s"', $classId)
            );
        }
    }

    /**
     * @param ClassWorksheetParser $parser
     */
    protected function checkMixedActions(ClassWorksheetParser $parser)
    {
        $this->assertEquals(
            $this->getExpectedMixedActions(),
            $parser->getActions(),
            'Parser did not create correct add actions'
        );
    }

    /**
     * @param ClassWorksheetParser $parser
     */
    protected function checkAddActions(ClassWorksheetParser $parser)
    {
        $this->assertEquals(
            $this->getExpectedAddActions(),
            $parser->getActions(),
            'Parser did not create correct add actions'
        );
    }

    public function testItShouldItShouldStoreClassesInTheRegistryAndCreateAddActions()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_good_sheet.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $this->assertFalse($parser->hasErrors(), 'Parser Reported errors for a good classes sheet');
        $this->assertEmpty($parser->getErrors(), 'Parser has errors reported for a good classes sheet');
        $this->assertFalse($parser->hasWarnings(), 'Parser Reported warnings for a good classes sheet');
        $this->assertEmpty($parser->getWarnings(), 'Parser has warnings reported for a good classes sheet');

        $this->checkRegistry();
        $this->checkAddActions($parser);
    }

    public function testItShouldItShouldStoreClassesInTheRegistryAndCreateMixedActions()
    {
        $this->registry->addClassroom(new ClassRoom('Second Grade', '201', ['8001', '8002', '8003'], new Group()));
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_good_sheet.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $this->assertFalse($parser->hasErrors(), 'Parser Reported errors for a good classes sheet');
        $this->assertEmpty($parser->getErrors(), 'Parser has errors reported for a good classes sheet');
        $this->assertFalse($parser->hasWarnings(), 'Parser Reported warnings for a good classes sheet');
        $this->assertEmpty($parser->getWarnings(), 'Parser has warnings reported for a good classes sheet');

        $this->checkMixedActions($parser);
    }

    public function testItShouldWarnOnEmptyLineAndStillCreateAddActions()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_good_sheet_with_empty_line.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedWarnings = [
            'Sheet "Classes" Row: 4 No data found between cells "A" and "D" Skipping this row',
            'Sheet "Classes" Row: 7 No data found between cells "A" and "D" Skipping this row',
            'Sheet "Classes" Row: 9 No data found between cells "A" and "D" Skipping this row',
        ];

        $this->assertFalse($parser->hasErrors(), 'Parser Reported errors for a good classes sheet');
        $this->assertEmpty($parser->getErrors(), 'Parser has errors reported for a good classes sheet');
        $this->assertTrue($parser->hasWarnings(), 'Parser did not Report warnings for a sheet with an empty');
        $this->assertSame(
            $expectedWarnings,
            $parser->getWarnings(),
            'Parser reported incorrect warnings for sheet with empty line'
        );

        $this->checkRegistry();
    }

    public function testItShouldErrorWithInvalidDdbnnn()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_invalid_ddbnnn.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Classes" Row: 2 Invalid DDBNNN "144Q1001"'];

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
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Classes" Row: 2 Missing "TITLE"'];

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
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Classes" Row: 2 Missing "OFF CLS"'];

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

    public function testItShouldErrorWhenMissingSheet()
    {
        $this->setExpectedException(
            InvalidWorksheetException::class,
            'Missing worksheet "Classes"'
        );

        $this->getParser(new \PHPExcel_Worksheet(new \PHPExcel()));
    }

    public function testItShouldErrorWithEmptyWorksheet()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Classes');
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Classes" Row: 1 Column "A" in the header is not labeled as "DDBNNN"',
            'Sheet "Classes" Row: 1 Is missing one or more column(s) between "A" and "D"',
        ];

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

    public function testItShouldReportErrorWithBadHeadeHeaderLables()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_bad_header.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Classes" Row: 1 Column "A" in the header is not labeled as "DDBNNN"',
            'Sheet "Classes" Row: 1 Column "B" in the header is not labeled as "TITLE"',
            'Sheet "Classes" Row: 1 Column "C" in the header is not labeled as "OFF CLS"',
            'Sheet "Classes" Row: 1 Column "D" in the header is not labeled as "SUB CLASSES"',
        ];

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

    public function testItShouldErrorWhenSubClassNotFound()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_missing_subclass.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Classes" A subclass with the id "8002" was not found for Class [001] "Lunch"',
            'Sheet "Classes" A subclass with the id "8002" was not found for Class [102] "PreK"',
            'Sheet "Classes" A subclass with the id "8002" was not found for Class [011] "Kindergarten"',
            'Sheet "Classes" A subclass with the id "8002" was not found for Class [101] "First Grade"',
            'Sheet "Classes" A subclass with the id "8003" was not found for Class [101] "First Grade"',
            'Sheet "Classes" A subclass with the id "8002" was not found for Class [201] "Second Grade"',
            'Sheet "Classes" A subclass with the id "8003" was not found for Class [201] "Second Grade"',
            'Sheet "Classes" A subclass with the id "8002" was not found for Class [301] "Third Grade"',
            'Sheet "Classes" A subclass with the id "8003" was not found for Class [301] "Third Grade"',
        ];

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
