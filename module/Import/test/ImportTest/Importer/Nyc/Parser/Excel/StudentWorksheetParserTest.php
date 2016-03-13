<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Application\Exception\NotFoundException;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Parser\AbstractParser;
use Import\Importer\Nyc\Parser\Excel\StudentWorksheetParser;
use Import\Importer\Nyc\Students\StudentRegistry;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test StudentWorksheetParserTest
 */
class StudentWorksheetParserTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\User\Service\UserServiceInterface
     */
    protected $userService;

    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var StudentRegistry
     */
    protected $registry;

    /**
     * @var ClassRoomRegistry
     */
    protected $classRegistry;

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = \Mockery::mock('\User\Service\UserServiceInterface');
        $this->userService->shouldReceive('fetchUserByExternalId')
            ->andThrow(NotFoundException::class)
            ->byDefault();
    }

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
    public function setUpStudentRegistry()
    {
        $this->registry = new StudentRegistry($this->userService);
    }

    /**
     * @before
     */
    public function setUpClassRegistry()
    {
        AbstractParser::clear();
        $this->classRegistry = new ClassRoomRegistry($this->groupService);
    }

    /**
     * @param \PHPExcel_Worksheet $sheet
     * @return StudentWorksheetParser
     */
    protected function getParser(\PHPExcel_Worksheet $sheet)
    {
        return new StudentWorksheetParser($sheet, $this->registry, $this->classRegistry);
    }

    public function testItShouldCreateCorrectActionsForStudent()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/student_good_sheet.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $class  = new ClassRoom('History of the world', '001');
        $this->classRegistry->addClassroom($class);

        $parser->preProcess();

        $this->assertFalse($parser->hasErrors(), 'Processor should not have errors on good file');
        $this->assertEmpty($parser->getErrors(), 'Student Processor is Reporting errors');
        $this->assertFalse($parser->hasWarnings(), 'Student Processor is Reporting warnings on good file');
        $this->assertEmpty($parser->getWarnings(), 'Student Processor is Reporting warnings');

        $setup = new StudentParserSetup();

        $this->assertEquals(
            $setup->getExpectedGoodActions($this->userService),
            $parser->getActions(),
            'Parser did not create actions correctly'
        );
    }

    public function testItShouldCreateCorrectActionsForStudentWithWarnings()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/student_good_sheet_with_blanks.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $class  = new ClassRoom('History of the world', '001');
        $this->classRegistry->addClassroom($class);

        $parser->preProcess();

        $expectedWarnings = [
            'Sheet "Students" Row: 3 No data found between cells "A" and "AC" Skipping this row',
            'Sheet "Students" Row: 6 No data found between cells "A" and "AC" Skipping this row',
        ];

        $this->assertFalse($parser->hasErrors(), 'Processor should not have errors on good file');
        $this->assertEmpty($parser->getErrors(), 'Student Processor is Reporting errors');
        $this->assertTrue($parser->hasWarnings(), 'Student Processor is Reporting warnings on good file');
        $this->assertEquals(
            $expectedWarnings,
            $parser->getWarnings(),
            'Student Processor did not report correct warnings'
        );

        $setup = new StudentParserSetup();

        $this->assertEquals(
            $setup->getExpectedGoodActionsForBlanks($this->userService),
            $parser->getActions(),
            'Parser did not create actions correctly'
        );
    }

    public function testItShouldErrorWithInvalidDdbnnn()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/student_invalid_ddbnnn.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet "Students" Row: 2 Invalid DDBNNN "144Q1001"'];

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

    public function testItShouldErrorWhenStudentMissingRequiredFields()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/student_missing_required_fields.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Students" Row: 2 Missing "LAST NAME"',
            'Sheet "Students" Row: 2 Missing "FIRST NAME"',
            'Sheet "Students" Row: 2 Missing "STUDENT ID"',
            'Sheet "Students" Row: 2 Missing "BIRTH DT"',
            'Sheet "Students" Row: 2 Missing "OFF CLS"',
            'Sheet "Students" Row: 2 Invalid birthday ""',
        ];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a student is missing required fields'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a student was missing required fields'
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

    public function testItShouldErrorWhenStudentHasInvalidBirthday()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/student_invalid_birthday.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Students" Row: 2 Invalid birthday "foo-bar"',
        ];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a student is missing a Birthday'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a student was missing a Birthday'
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

    public function testItShouldErrorWhenTwoStudentsHaveTheSameId()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/student_duplicate_id.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $class  = new ClassRoom('History of the world', '001');
        $this->classRegistry->addClassroom($class);

        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Students" Row: 4 A student with the id "foo-bar" appears more than once in this sheet',
        ];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when two students have the same student id'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages for duplicate students'
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
