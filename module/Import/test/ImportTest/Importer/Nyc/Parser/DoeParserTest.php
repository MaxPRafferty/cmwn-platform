<?php

namespace ImportTest\Importer\Nyc\Parser;

use Application\Exception\NotFoundException;
use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Parser\DoeParser;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test DoeParserTest
 */
class DoeParserTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\ClassRoom\ClassRoomRegistry
     */
    protected $classRegistry;

    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Teachers\TeacherRegistry
     */
    protected $teacherRegistry;

    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Students\StudentRegistry
     */
    protected $studentRegistry;

    /**
     * @var \Mockery\MockInterface|\Group\Service\UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var Group
     */
    protected $school;

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->userGroupService = \Mockery::mock('\Group\Service\UserGroupServiceInterface');
    }
    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\GroupServiceInterface');
    }

    /**
     * @before
     */
    public function setUpClassRegistry()
    {
        /** @var \Mockery\MockInterface|\Group\Service\GroupService $groupService */
        $groupService = \Mockery::mock('\Group\Service\GroupService');
        $groupService->shouldReceive('fetchGroupByExternalId')
            ->andThrow(NotFoundException::class)
            ->byDefault();

        $this->classRegistry = new ClassRoomRegistry($groupService);
    }


    /**
     * @before
     */
    public function setUpTeacherRegistry()
    {
        /** @var \Mockery\MockInterface|\User\Service\UserServiceInterface $userService */
        $userService = \Mockery::mock('\User\Service\UserServiceInterface');
        $userService->shouldReceive('fetchUserByEmail')
            ->andThrow(NotFoundException::class)
            ->byDefault();

        $this->teacherRegistry = new TeacherRegistry($userService, $this->classRegistry);
    }

    /**
     * @before
     */
    public function setUpStudentRegistry()
    {
        /** @var \Mockery\MockInterface|\User\Service\UserServiceInterface $userService */
        $userService = \Mockery::mock('\User\Service\UserServiceInterface');
        $userService->shouldReceive('fetchUserByExternalId')
            ->andThrow(NotFoundException::class)
            ->byDefault();

        $this->studentRegistry = new StudentRegistry($userService, $this->classRegistry);
    }

    /**
     * @before
     */
    public function setUpSchoolGroup()
    {
        $this->school = new Group();
        $this->school->setGroupId('manchuck');
        $this->school->setTitle('MANCHUCK School of Rock');
    }

    /**
     * @return DoeParser|\Mockery\MockInterface
     */
    protected function getParser()
    {
        DoeParser::clear();
        $parser = new DoeParser(
            $this->classRegistry,
            $this->teacherRegistry,
            $this->studentRegistry,
            $this->userGroupService,
            $this->groupService
        );

        $parser->setSchool($this->school);
        return $parser;
    }

    /**
     * @dataProvider missingSheets
     */
    public function testItShouldNotCallParsersWhenSheetsNotFound($fileName)
    {
        $parser = $this->getParser();
        $parser->setFileName($fileName);

        $parser->preProcess();

        $this->assertTrue($parser->hasErrors(), 'Doe Parser is not in error state when missing required sheets');
        $this->assertNotEmpty($parser->getErrors(), 'Doe Parser is not reporting errors');

        $this->assertFalse($parser->hasWarnings(), 'Doe Parser is reporting warnings');
        $this->assertEmpty($parser->getWarnings(), 'Doe Parser has warnings');
    }
    
    public function testItShouldMergeErrorsAndWarningsFromParsers()
    {
        $parser = $this->getParser();
        $parser->setFileName(__DIR__ . '/_files/error_sheet.xlsx');
        $parser->preProcess();

        $expectedErrors = [
            'Sheet "Classes" Row: 1 Column "B" in the header is not labeled as "TITLE"',
            'Sheet "Teachers" Row: 1 Column "B" in the header is not labeled as "TYPE"',
            'Sheet "Students" Row: 1 Column "C" in the header is not labeled as "FIRST NAME"',
        ];

        $this->assertTrue(
            $parser->hasErrors(),
            'Doe Parser is not reporting errors when parsers have errors'
        );

        $this->assertEquals(
            $expectedErrors,
            $parser->getErrors(),
            'Doe Parser did not merge errors from parsers'
        );
    }
    
    public function testItShouldMergeActionsFromParsers()
    {
        $parser = $this->getParser();
        $parser->setFileName(__DIR__ . '/_files/test_sheet.xlsx');
        $parser->preProcess();

        $this->assertFalse(
            $parser->hasErrors(),
            'Doe parser reported errors'
        );

        $this->assertFalse(
            $parser->hasWarnings(),
            'Doe Parser is reporting warnings'
        );

        $this->assertEquals(8, count($parser->getActions()), 'Parser did not merge actions');

    }

    public function testItShouldMergeActionsFromParsersAndReportWarningForExtraSheet()
    {
        $parser = $this->getParser();
        $parser->setFileName(__DIR__ . '/_files/extra_sheet.xlsx');
        $parser->preProcess();

        $this->assertFalse(
            $parser->hasErrors(),
            'Doe parser reported errors'
        );

        $this->assertTrue(
            $parser->hasWarnings(),
            'Doe Parser is not reporting warnings for extra sheet'
        );

        $this->assertEquals(
            ['Sheet with the name "Foo Bar" was found and will be ignored'],
            $parser->getWarnings(),
            'Doe Parser did not report warning for extra sheet'
        );

        $this->assertEquals(8, count($parser->getActions()), 'Parser did not merge actions');
    }

    public function testItShouldThrowExceptionWhenFileNameNotSet()
    {
        $this->setExpectedException(\RuntimeException::class);
        $this->getParser()->preProcess();
    }

    /**
     * @return array
     */
    public function missingSheets()
    {
        return [
            'Missing Classes Sheet'  => [__DIR__ . '/_files/missing_class_sheet.xlsx'],
            'Missing Teachers Sheet' => [__DIR__ . '/_files/missing_teacher_sheet.xlsx'],
            'Missing Student Sheet'  => [__DIR__ . '/_files/missing_student_sheet.xlsx'],
        ];
    }
}
