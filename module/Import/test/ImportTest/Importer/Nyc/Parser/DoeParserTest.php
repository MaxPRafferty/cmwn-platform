<?php

namespace ImportTest\Importer\Nyc\Parser;

use Application\Exception\NotFoundException;
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
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser
     */
    protected $classParser;

    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Parser\Excel\TeacherWorksheetParser
     */
    protected $teacherParser;

    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Parser\Excel\StudentWorksheetParser
     */
    protected $studentParser;

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
    public function setUpClassParserMock()
    {
        $this->classParser = \Mockery::mock('Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser');
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
        $userService->shouldReceive('fetchUserByEmail')
            ->andThrow(NotFoundException::class)
            ->byDefault();

        $this->studentRegistry = new StudentRegistry($userService, $this->classRegistry);
    }
    
    /**
     * @before
     */
    public function setUpClassParser()
    {
        $this->classParser = \Mockery::mock('\Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser');
        $this->classParser->shouldReceive('preProcess')
            ->byDefault();

        $this->classParser->shouldReceive('hasErrors')
            ->andReturn(false)
            ->byDefault();

        $this->classParser->shouldReceive('hasWarnings')
            ->andReturn(false)
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpTeacherParser()
    {
        $this->teacherParser = \Mockery::mock('\Import\Importer\Nyc\Parser\Excel\TeacherWorksheetParser');
        $this->teacherParser->shouldReceive('preProcess')
            ->byDefault();

        $this->teacherParser->shouldReceive('hasErrors')
            ->andReturn(false)
            ->byDefault();

        $this->teacherParser->shouldReceive('hasWarnings')
            ->andReturn(false)
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpStudentParser()
    {
        $this->studentParser = \Mockery::mock('\Import\Importer\Nyc\Parser\Excel\StudentWorksheetParser');
        $this->studentParser->shouldReceive('preProcess')
            ->byDefault();

        $this->studentParser->shouldReceive('hasErrors')
            ->andReturn(false)
            ->byDefault();

        $this->studentParser->shouldReceive('hasWarnings')
            ->andReturn(false)
            ->byDefault();
    }

    /**
     * @return DoeParser|\Mockery\MockInterface
     */
    protected function getParser()
    {
        /** @var \Mockery\MockInterface|\Import\Importer\Nyc\Parser\DoeParser $parser */
        $parser = \Mockery::mock(
            '\Import\Importer\Nyc\Parser\DoeParser[getClassParser,getStudentParser,getTeacherParser]',
            [$this->classRegistry, $this->teacherRegistry, $this->studentRegistry, $this->userGroupService]
        );

        $parser->shouldReceive('getClassParser')
            ->andReturn($this->classParser)
            ->byDefault();

        $parser->shouldReceive('getTeacherParser')
            ->andReturn($this->teacherParser)
            ->byDefault();

        $parser->shouldReceive('getStudentParser')
            ->andReturn($this->studentParser)
            ->byDefault();

        return $parser;
    }

    /**
     * @dataProvider missingSheets
     */
    public function testItShouldNotCallParsersWhenSheetsNotFound($fileName)
    {
        $this->classParser->shouldReceive('preProcess')->never();
        $this->teacherParser->shouldReceive('preProcess')->never();
        $this->studentParser->shouldReceive('preProcess')->never();
        
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
        // classes
        $this->classParser->shouldReceive('hasErrors')
            ->atLeast(1)
            ->andReturn(true);
        
        $this->classParser->shouldReceive('getErrors')
            ->andReturn(['class parser error']);
            
        $this->classParser->shouldReceive('hasWarnings')
            ->atLeast(1)
            ->andReturn(true);
        
        $this->classParser->shouldReceive('getWarnings')
            ->andReturn(['class parser warning']);

        $this->classParser->shouldNotReceive('getActions');

        // Teachers
        $this->teacherParser->shouldReceive('hasErrors')
            ->atLeast(1)
            ->andReturn(true);
        
        $this->teacherParser->shouldReceive('getErrors')
            ->andReturn(['teacher parser error']);
            
        $this->teacherParser->shouldReceive('hasWarnings')
            ->atLeast(1)
            ->andReturn(true);
        
        $this->teacherParser->shouldReceive('getWarnings')
            ->andReturn(['teacher parser warning']);

        $this->teacherParser->shouldNotReceive('getActions');

        // Students
        $this->studentParser->shouldReceive('hasErrors')
            ->atLeast(1)
            ->andReturn(true);
        
        $this->studentParser->shouldReceive('getErrors')
            ->andReturn(['student parser error']);
            
        $this->studentParser->shouldReceive('hasWarnings')
            ->atLeast(1)
            ->andReturn(true);
        
        $this->studentParser->shouldReceive('getWarnings')
            ->andReturn(['student parser warning']);

        $this->studentParser->shouldNotReceive('getActions');


        $parser = $this->getParser();
        $parser->setFileName(__DIR__ . '/_files/test_sheet.xlsx');
        $parser->preProcess();

        $expectedErrors = [
            'class parser error',
            'teacher parser error',
            'student parser error',
        ];

        $expectedWarnings = [
            'class parser warning',
            'teacher parser warning',
            'student parser warning',
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

        $this->assertTrue(
            $parser->hasWarnings(),
            'Doe Parser is not reporting warnings when parsers have warnings'
        );

        $this->assertEquals(
            $expectedWarnings,
            $parser->getWarnings(),
            'Doe Parser is not merging warnings from parsers'
        );
    }
    
    public function testItShouldMergeActionsFromParsers()
    {
        /** @var \Mockery\MockInterface|\Import\ActionInterface $classAction */
        $classAction = \Mockery::mock('\Import\ActionInterface');

        /** @var \Mockery\MockInterface|\Import\ActionInterface $teacherAction */
        $teacherAction = \Mockery::mock('\Import\ActionInterface');

        /** @var \Mockery\MockInterface|\Import\ActionInterface $studentAction */
        $studentAction = \Mockery::mock('\Import\ActionInterface');

        $this->classParser->shouldReceive('getActions')
            ->once()
            ->andReturn([$classAction]);

        $this->teacherParser->shouldReceive('getActions')
            ->once()
            ->andReturn([$teacherAction]);

        $this->studentParser->shouldReceive('getActions')
            ->once()
            ->andReturn([$teacherAction]);

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

        $expectedActions = [
            $classAction,
            $teacherAction,
            $studentAction
        ];

        $this->assertEquals($expectedActions, $parser->getActions(), 'Parser did not merge actions');
    }

    public function testItShouldMergeActionsFromParsersAndReportWarningForExtraSheet()
    {
        /** @var \Mockery\MockInterface|\Import\ActionInterface $classAction */
        $classAction = \Mockery::mock('\Import\ActionInterface');

        /** @var \Mockery\MockInterface|\Import\ActionInterface $teacherAction */
        $teacherAction = \Mockery::mock('\Import\ActionInterface');

        /** @var \Mockery\MockInterface|\Import\ActionInterface $studentAction */
        $studentAction = \Mockery::mock('\Import\ActionInterface');

        $this->classParser->shouldReceive('getActions')
            ->once()
            ->andReturn([$classAction]);

        $this->teacherParser->shouldReceive('getActions')
            ->once()
            ->andReturn([$teacherAction]);

        $this->studentParser->shouldReceive('getActions')
            ->once()
            ->andReturn([$teacherAction]);

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

        $expectedActions = [
            $classAction,
            $teacherAction,
            $studentAction
        ];

        $this->assertEquals($expectedActions, $parser->getActions(), 'Parser did not merge actions');
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
