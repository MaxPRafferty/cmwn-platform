<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Application\Exception\NotFoundException;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Parser\AbstractParser;
use Import\Importer\Nyc\Parser\Excel\TeacherWorksheetParser;
use Import\Importer\Nyc\Teachers\Teacher;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;

/**
 * Test TeacherWorksheetParserTest
 * @group Teacher
 * @group User
 * @group NycImport
 * @group Group
 * @group Registry
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TeacherWorksheetParserTest extends TestCase
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
     * @var TeacherRegistry
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
        $this->userService->shouldReceive('fetchUserByEmail')
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
    public function setUpTeacherRegistry()
    {
        $this->registry = new TeacherRegistry($this->userService);
    }

    /**
     * @before
     */
    public function setUpClassRegistry()
    {
        $this->classRegistry = new ClassRoomRegistry($this->groupService);
        $this->classRegistry->setOrganization('foo-bar');
    }

    /**
     * @param \PHPExcel_Worksheet $sheet
     * @return TeacherWorksheetParser
     */
    protected function getParser(\PHPExcel_Worksheet $sheet)
    {
        AbstractParser::clear();
        return new TeacherWorksheetParser($sheet, $this->registry, $this->classRegistry);
    }

    /**
     * @test
     */
    public function testItShouldCreateCorrectActionsForTeacher()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_good_sheet.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $class  = new ClassRoom('History of the world', '01X100-001');
        $this->classRegistry->addClassroom($class);

        $parser->preProcess();

        $this->assertFalse($parser->hasErrors(), 'Processor should not have errors on good file');
        $this->assertEmpty($parser->getErrors(), 'Teacher Processor is Reporting errors');
        $this->assertFalse($parser->hasWarnings(), 'Teacher Processor is Reporting warnings on good file');
        $this->assertEmpty($parser->getWarnings(), 'Teacher Processor is Reporting warnings');

        $setup = new TeacherParserSetup();

        $this->assertEquals(
            $setup->getExpectedGoodActions($this->userService),
            $parser->getActions(),
            'Parser did not create actions correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateCorrectActionsForSomeExistingTeachers()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_good_sheet.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $class  = new ClassRoom('History of the world', '01X100-001');
        $this->classRegistry->addClassroom($class);

        $teacher = new Teacher();
        $teacher->setRole('Assistant Principal')
            ->setFirstName('Carol')
            ->setLastName('Smitt')
            ->setEmail('smith@gmail.com')
            ->setGender('F')
            ->setUser(new Adult());

        $this->assertFalse($teacher->isNew());
        $this->registry->addTeacher($teacher);
        $parser->preProcess();

        $this->assertFalse($parser->hasErrors(), 'Processor should not have errors on good file');
        $this->assertEmpty($parser->getErrors(), 'Teacher Processor is Reporting errors');
        $this->assertFalse($parser->hasWarnings(), 'Teacher Processor is Reporting warnings on good file');
        $this->assertEmpty($parser->getWarnings(), 'Teacher Processor is Reporting warnings');

        $setup = new TeacherParserSetup();

        $this->assertEquals(
            $setup->getExpectedMixedActions($this->userService),
            $parser->getActions(),
            'Parser did not create actions correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateCorrectActionsForTeacherWithWarnings()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_good_sheet_with_warnings.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $class  = new ClassRoom('History of the world', '01X100-001');
        $this->classRegistry->addClassroom($class);

        $parser->preProcess();

        // @codingStandardsIgnoreStart
        $expectedWarnings = [
            0 => 'Sheet <b>"Teachers"</b> Row: <b>5</b> No data found between cells <b>"A"</b> and <b>"H"</b> Skipping this row',
            1 => 'Sheet <b>"Teachers"</b> Row: <b>7</b> No data found between cells <b>"A"</b> and <b>"H"</b> Skipping this row',
        ];
        // @codingStandardsIgnoreEnd

        $this->assertFalse($parser->hasErrors(), 'Processor should not have errors on good file');
        $this->assertEmpty($parser->getErrors(), 'Teacher Processor is Reporting errors');
        $this->assertTrue($parser->hasWarnings(), 'Teacher Processor is Not reporting warnings');
        $this->assertEquals(
            $expectedWarnings,
            $parser->getWarnings(),
            'Teacher Processor did not report correct warnings'
        );

        $setup = new TeacherParserSetup();

        $this->assertEquals(
            $setup->getExpectedWarningActions($this->userService),
            $parser->getActions(),
            'Parser did not create actions correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldErrorWithInvalidDdbnnn()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_invalid_ddbnnn.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Teachers"</b> Row: <b>2</b> Invalid <b>DDBNNN "144Q1001"</b>'];

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
    public function testItShouldErrorWhenTeacherMissingFirstName()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_missing_first_name.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Teachers"</b> Row: <b>2</b> Missing <b>"FIRST NAME"</b>'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a teacher is missing a First Name'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a teacher was missing a First Name'
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
    public function testItShouldErrorWhenTeacherMissingLastName()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_missing_last_name.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Teachers"</b> Row: <b>2</b> Missing <b>"LAST NAME"</b>'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a teacher is missing a Last Name'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a teacher was missing a Last Name'
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
    public function testItShouldErrorWhenTeacherMissingEmail()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_missing_email.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Teachers"</b> Row: <b>2</b> Missing <b>"EMAIL"</b>'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a teacher is missing a email'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a teacher was missing a email'
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
    public function testItShouldErrorWhenTeacherHasInvalidClass()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_invalid_class.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Teachers"</b> Row: <b>2</b> Class ID <b>"44Q001-001"</b> was not found'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a teacher has invalid a class'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a teacher  has invalid a class'
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
    public function testItShouldErrorWhenTeacherHasInvalidEmail()
    {
        $reader = \PHPExcel_IOFactory::load(__DIR__ . '/_files/teacher_invalid_email.xlsx');
        $sheet  = $reader->getSheet(0);
        $parser = $this->getParser($sheet);
        $parser->preProcess();

        $expectedErrors = ['Sheet <b>"Teachers"</b> Row: <b>2</b> Teacher has invalid email <b>"foo-bar"</b>'];

        $this->assertTrue(
            $parser->hasErrors(),
            'Parser did not produce any errors when a teacher has invalid a email'
        );

        $this->assertSame(
            $expectedErrors,
            $parser->getErrors(),
            'Parser did not report correct error messages when a teacher  has invalid a email'
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
