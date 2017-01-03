<?php

namespace ImportTest\Importer\Nyc\Parser;

use Application\Exception\NotFoundException;
use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Parser\DoeParser;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authorization\Rbac;

/**
 * Test DoeParserTest
 *
 * @group Import
 * @group ClassRoom
 * @group Teacher
 * @group Student
 * @group Group
 * @group User
 * @group NycImport
 * @group Excel
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
     * @var \Mockery\MockInterface|\Security\Service\SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @var Rbac
     */
    protected $rbac;

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
        $this->classRegistry->setNetworkId('foo-bar');
    }

    /**
     * @before
     */
    public function setUpSecurityService()
    {
        /** @var  $this->securityService */
        $this->securityService = \Mockery::mock('\Security\Service\SecurityServiceInterface');
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
    public function setUpRbac()
    {
        $this->markTestSkipped('This should not use the helper to get the rbac');
        $this->rbac = TestHelper::getServiceManager()->get(Rbac::class);
    }

    /**
     * @before
     */
    public function setUpSchoolGroup()
    {
        $this->school = new Group();
        $this->school->setGroupId('manchuck');
        $this->school->setTitle('MANCHUCK School of Rock');
        $this->school->setOrganizationId('foo-bar');
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
            $this->groupService,
            $this->securityService
        );

        $parser->setSchool($this->school);
        $parser->setStudentCode('foo_bar');
        $parser->setTeacherCode('baz_bat');
        $parser->setRbac($this->rbac);

        return $parser;
    }

    /**
     * @dataProvider missingSheets
     * @param $fileName
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

    /**
     * @test
     */
    public function testItShouldMergeErrorsAndWarningsFromParsers()
    {
        $parser = $this->getParser();
        $parser->setFileName(__DIR__ . '/_files/error_sheet.xlsx');
        $parser->preProcess();

        // @codingStandardsIgnoreStart
        $expectedErrors = [
            'Sheet <b>"Classes"</b> Row: <b>1</b> Column <b>"B"</b> in the header is not labeled as <b>"TITLE"</b>',
            'Sheet <b>"Teachers"</b> Row: <b>1</b> Column <b>"B"</b> in the header is not labeled as <b>"TYPE"</b>',
            'Sheet <b>"Students"</b> Row: <b>1</b> Column <b>"C"</b> in the header is not labeled as <b>"FIRST NAME"</b>',
        ];
        // @codingStandardsIgnoreEnd

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

    /**
     * @test
     */
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

        $this->assertEquals(11, count($parser->getActions()), 'Parser did not merge actions');
    }

    /**
     * @test
     */
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
            ['Sheet with the name <b>"Foo Bar"</b> was found and will be ignored'],
            $parser->getWarnings(),
            'Doe Parser did not report warning for extra sheet'
        );

        $this->assertEquals(11, count($parser->getActions()), 'Parser did not merge actions');
    }

    /**
     * @test
     */
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
