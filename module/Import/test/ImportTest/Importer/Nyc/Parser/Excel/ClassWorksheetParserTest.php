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
 * Test ClassWorksheetParserTest
 *
 * @group Import
 * @group NycImport
 * @group Group
 * @group ClassRoom
 * @group Excel
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
            '01X100-001'  => new ClassRoom('Lunch', '01X100-001', ['01X100-8001', '01X100-8002']),
            '01X100-102'  => new ClassRoom('PreK', '01X100-102', ['01X100-8001', '01X100-8002']),
            '01X100-011'  => new ClassRoom('Kindergarten', '01X100-011', ['01X100-8001', '01X100-8002']),
            '01X100-101'  => new ClassRoom('First Grade', '01X100-101', ['01X100-8001', '01X100-8002', '01X100-8003']),
            '01X100-201'  => new ClassRoom('Second Grade', '01X100-201', ['01X100-8001', '01X100-8002', '01X100-8003']),
            '01X100-301'  => new ClassRoom('Third Grade', '01X100-301', ['01X100-8001', '01X100-8002', '01X100-8003']),
            '01X100-8001' => new ClassRoom('Physical Education', '01X100-8001', []),
            '01X100-8002' => new ClassRoom('Art', '01X100-8002', []),
            '01X100-8003' => new ClassRoom('Technology', '01X100-8003', []),
        ];
    }

    /**
     * @return \SplPriorityQueue|AddClassRoomAction[]
     */
    protected function getExpectedAddActions()
    {
        $actions = new \SplPriorityQueue();

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Lunch', '01X100-001', ['01X100-8001', '01X100-8002'])
            ),
            100
        );


        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('PreK', '01X100-102', ['01X100-8001', '01X100-8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Kindergarten', '01X100-011', ['01X100-8001', '01X100-8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('First Grade', '01X100-101', ['01X100-8001', '01X100-8002', '01X100-8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Second Grade', '01X100-201', ['01X100-8001', '01X100-8002', '01X100-8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Third Grade', '01X100-301', ['01X100-8001', '01X100-8002', '01X100-8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Physical Education', '01X100-8001', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Art', '01X100-8002', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Technology', '01X100-8003', [])
            ),
            100
        );

        return $actions;
    }
    /**
     * @return \SplPriorityQueue|AddClassRoomAction[]
     */
    protected function getExpectedMixedActions()
    {
        $actions = new \SplPriorityQueue();

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Lunch', '01X100-001', ['01X100-8001', '01X100-8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('PreK', '01X100-102', ['01X100-8001', '01X100-8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Kindergarten', '01X100-011', ['01X100-8001', '01X100-8002'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('First Grade', '01X100-101', ['01X100-8001', '01X100-8002', '01X100-8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Third Grade', '01X100-301', ['01X100-8001', '01X100-8002', '01X100-8003'])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Physical Education', '01X100-8001', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Art', '01X100-8002', [])
            ),
            100
        );

        $actions->insert(
            new AddClassRoomAction(
                $this->groupService,
                new ClassRoom('Technology', '01X100-8003', [])
            ),
            100
        );

        return $actions;
    }

    /**
     * Helper to check the registry is correct
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testItShouldItShouldStoreClassesInTheRegistryAndCreateMixedActions()
    {
        $this->registry->addClassroom(
            new ClassRoom(
                'Second Grade',
                '01X100-201',
                ['01X100-8001', '01X100-8002', '01X100-8003'],
                new Group()
            )
        );
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

    /**
     * @test
     */
    public function testItShouldWarnOnEmptyLineAndStillCreateAddActions()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_good_sheet_with_empty_line.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        // @codingStandardsIgnoreStart
        $expectedWarnings = [
            'Sheet <b>"Classes"</b> Row: <b>4</b> No data found between cells <b>"A"</b> and <b>"D"</b> Skipping this row',
            'Sheet <b>"Classes"</b> Row: <b>7</b> No data found between cells <b>"A"</b> and <b>"D"</b> Skipping this row',
            'Sheet <b>"Classes"</b> Row: <b>9</b> No data found between cells <b>"A"</b> and <b>"D"</b> Skipping this row',
        ];
        // @codingStandardsIgnoreEnd

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

    /**
     * @test
     */
    public function testItShouldErrorWithInvalidDdbnnn()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_invalid_ddbnnn.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Classes"</b> Row: <b>2</b> Invalid <b>DDBNNN "144Q1001"</b>'];

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

    /**
     * @test
     */
    public function testItShouldErrorWhenClassMissingTitle()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_missing_title.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Classes"</b> Row: <b>2</b> Missing <b>"TITLE"</b>'];

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

    /**
     * @test
     */
    public function testItShouldErrorWhenClassMissingId()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_missing_class_id.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Classes"</b> Row: <b>2</b> Missing <b>"OFF CLS"</b>'];

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

    /**
     * @test
     */
    public function testItShouldErrorWhenMissingSheet()
    {
        $this->setExpectedException(
            InvalidWorksheetException::class,
            'Missing worksheet "Classes"'
        );

        $this->getParser(new \PHPExcel_Worksheet(new \PHPExcel()));
    }

    /**
     * @test
     */
    public function testItShouldErrorWithEmptyWorksheet()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('Classes');
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        // @codingStandardsIgnoreStart
        $expectedErrors = [
            'Sheet <b>"Classes"</b> Row: <b>1</b> Column <b>"A"</b> in the header is not labeled as <b>"DDBNNN"</b>',
            'Sheet <b>"Classes"</b> Row: <b>1</b> Is missing one or more column(s) between <b>"A"</b> and <b>"D"</b>',
        ];
        // @codingStandardsIgnoreEnd

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

    /**
     * @test
     */
    public function testItShouldReportErrorWithBadHeaderHeaderLabels()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_bad_header.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        // @codingStandardsIgnoreStart
        $expectedErrors = [
            'Sheet <b>"Classes"</b> Row: <b>1</b> Column <b>"A"</b> in the header is not labeled as <b>"DDBNNN"</b>',
            'Sheet <b>"Classes"</b> Row: <b>1</b> Column <b>"B"</b> in the header is not labeled as <b>"TITLE"</b>',
            'Sheet <b>"Classes"</b> Row: <b>1</b> Column <b>"C"</b> in the header is not labeled as <b>"OFF CLS"</b>',
            'Sheet <b>"Classes"</b> Row: <b>1</b> Column <b>"D"</b> in the header is not labeled as <b>"SUB CLASSES"</b>',
        ];
        // @codingStandardsIgnoreEnd

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

    /**
     * @test
     */
    public function testItShouldErrorWhenSubClassNotFound()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/class_missing_subclass.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        // @codingStandardsIgnoreStart
        $expectedErrors = [
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8002"</b> was not found for Class [<b>01X100-001</b>] "<b>Lunch</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8002"</b> was not found for Class [<b>01X100-102</b>] "<b>PreK</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8002"</b> was not found for Class [<b>01X100-011</b>] "<b>Kindergarten</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8002"</b> was not found for Class [<b>01X100-101</b>] "<b>First Grade</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8003"</b> was not found for Class [<b>01X100-101</b>] "<b>First Grade</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8002"</b> was not found for Class [<b>01X100-201</b>] "<b>Second Grade</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8003"</b> was not found for Class [<b>01X100-201</b>] "<b>Second Grade</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8002"</b> was not found for Class [<b>01X100-301</b>] "<b>Third Grade</b>"',
            'Sheet <b>"Classes"</b> A subclass with the id <b>"01X100-8003"</b> was not found for Class [<b>01X100-301</b>] "<b>Third Grade</b>"',
        ];
        // @codingStandardsIgnoreEnd

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
