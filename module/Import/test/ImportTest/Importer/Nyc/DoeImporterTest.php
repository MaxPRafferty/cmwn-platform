<?php

namespace ImportTest\Importer\Nyc;

use Application\Utils\Date\DateTimeFactory;
use Group\Group;
use Group\GroupInterface;
use Import\Importer\Nyc\DoeImporter;
use Import\ProcessorErrorException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Zend\EventManager\Event;

/**
 * Test NycDoeImporterTest
 *
 * @group Import
 * @group Excel
 * @group User
 * @group Group
 * @group Action
 * @group NycImport
 */
class DoeImporterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Import\Importer\Nyc\Parser\DoeParser
     */
    protected $parser;

    /**
     * @var DoeImporter
     */
    protected $importer;

    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var GroupInterface
     */
    protected $school;

    /**
     * @before
     */
    public function setUpImporter()
    {
        $this->importer = new DoeImporter($this->parser, $this->groupService);
        $this->importer->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpDoeParser()
    {
        $this->parser = \Mockery::mock('\Import\Importer\Nyc\Parser\DoeParser');
        $this->parser->shouldReceive('setLogger')->byDefault();
        $this->parser->shouldReceive('setSchool')->with($this->school)->byDefault();
        $this->parser->shouldReceive('setStudentCode')->byDefault();
        $this->parser->shouldReceive('setTeacherCode')->byDefault();
        $this->parser->shouldReceive('setFileName')->byDefault();
        $this->parser->shouldReceive('hasWarnings')->andReturn(false)->byDefault();
        $this->parser->shouldReceive('hasErrors')->andReturn(false)->byDefault();
        $this->parser->shouldReceive('getWarnings')->andReturn([])->byDefault();
        $this->parser->shouldReceive('getErrors')->andReturn([])->byDefault();
        $this->parser->shouldReceive('setEmail')->byDefault();
    }

    /**
     * @before
     */
    public function setUpSchool()
    {
        $this->school = new Group();
        $this->school->setGroupId('school');
        $this->school->setTitle('MANCHUCK School of Rock');
        $this->school->setOrganizationId('district');
    }

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\GroupServiceInterface');
        $this->groupService->shouldReceive('fetchGroup')
            ->with('school')
            ->andReturn($this->school)
            ->byDefault();
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams(),
        ];
    }

    /**
     * @test
     */
    public function testItShouldPassAlongFileInExchangeArray()
    {
        $now = DateTimeFactory::factory('now');
        $this->groupService->shouldReceive('fetchGroup')
            ->with('school')
            ->once()
            ->andReturn($this->school);

        $fileName = '/path/to/file.xsls';
        $this->assertEquals(
            [
                'type'         => 'Import\Importer\Nyc\DoeImporter',
                'file'         => null,
                'teacher_code' => null,
                'student_code' => null,
                'school'       => null,
                'email'        => null,
                'code_start'   => $now->format("Y-m-d H:i:s"),
            ],
            $this->importer->getArrayCopy()
        );

        $this->importer->exchangeArray([
            'type'         => 'Import\Importer\Nyc\DoeImporter',
            'file'         => $fileName,
            'teacher_code' => 'tCode',
            'student_code' => 'sCode',
            'school'       => 'school',
            'email'        => 'chuck@manchuck.com',
            'code_start'   => $now->format("Y-m-d H:i:s"),
        ]);

        $this->assertEquals(
            [
                'type'         => 'Import\Importer\Nyc\DoeImporter',
                'file'         => $fileName,
                'teacher_code' => 'tCode',
                'student_code' => 'sCode',
                'school'       => $this->school->getGroupId(),
                'email'        => 'chuck@manchuck.com',
                'code_start'   => $now->format("Y-m-d H:i:s"),
            ],
            $this->importer->getArrayCopy()
        );
    }

    /**
     * @test
     */
    public function testItShouldSetFileNameUsingFiles()
    {
        $fileName = '/path/to/file.xsls';
        $files    = [
            'name'     => 'my file',
            'tmp_name' => $fileName,
            'type'     => 'foo/bar',
            'error'    => UPLOAD_ERR_OK,
        ];

        $this->importer->setFileName($files);

        $this->assertEquals(
            $fileName,
            $this->importer->getFileName(),
            'Importer did not use $_FILES for setting file name'
        );
    }

    /**
     * @test
     */
    public function testItShouldExecuteActions()
    {

        $fileName = '/path/to/file.xsls';
        /** @var \Mockery\MockInterface|\Import\ActionInterface $action */
        $action = \Mockery::mock('\Import\ActionInterface');

        $action->shouldReceive('execute')->once();

        $actionQueue = new \SplPriorityQueue();
        $actionQueue->insert($action, 1);

        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->once();
        $this->parser->shouldReceive('getActions')->once()->andReturn($actionQueue);
        $this->parser->shouldReceive('hasErrors')->once()->andReturn(false);

        $this->importer->setFileName($fileName);
        $this->importer->setGroup($this->school);
        $this->importer->perform();

        $this->assertEquals(3, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.run',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[1]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.complete',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[2]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotExecuteActionsWhenParserProducesErrors()
    {
        $fileName = '/path/to/file.xsls';
        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->once();
        $this->parser->shouldReceive('getActions')->never();
        $this->parser->shouldReceive('hasErrors')->atLeast(1)->andReturn(true);
        $this->parser->shouldReceive('getErrors')->atLeast(1)->andReturn(['I am Error']);

        $this->importer->setFileName($fileName);
        $this->importer->setGroup($this->school);
        try {
            $this->importer->perform();
            $this->fail('Importer did not re-throw exception');
        } catch (ProcessorErrorException $processException) {
            // no op
        }

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.error',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotExecuteActionsWhenEventStops()
    {
        $fileName = '/path/to/file.xsls';
        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->never();
        $this->parser->shouldReceive('getActions')->never();
        $this->parser->shouldReceive('hasErrors')->never()->andReturn(true);

        $this->importer->getEventManager()->attach('nyc.import.excel', function (Event $event) {
            $event->stopPropagation(true);
        });
        $this->importer->setFileName($fileName);
        $this->importer->setGroup($this->school);
        $this->importer->perform();

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotExecuteActionsWhenParserInDryRun()
    {
        $fileName = '/path/to/file.xsls';
        /** @var \Mockery\MockInterface|\Import\ActionInterface $action */
        $action = \Mockery::mock('\Import\ActionInterface');

        $action->shouldReceive('execute')->never();

        $actionQueue = new \SplPriorityQueue();
        $actionQueue->insert($action, 1);

        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->once();
        $this->parser->shouldReceive('getActions')->once()->andReturn($actionQueue);
        $this->parser->shouldReceive('hasErrors')->once()->andReturn(false);

        $this->importer->setDryRun(true);
        $this->importer->setFileName($fileName);
        $this->importer->setGroup($this->school);
        $this->importer->perform();

        $this->assertEquals(3, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.run',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[1]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.complete',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[2]
        );
    }

    /**
     * @test
     */
    public function testItShouldPassOrgIdToActionWhenActionOrgAware()
    {
        $fileName = '/path/to/file.xsls';
        /** @var \Mockery\MockInterface|\Import\ActionInterface $action */
        $action = \Mockery::mock('\Import\ActionInterface, \Org\OrgAwareInterface');

        $action->shouldReceive('execute')->once();
        $action->shouldReceive('setOrgId')->once()->with($this->school->getOrganizationId());

        $actionQueue = new \SplPriorityQueue();
        $actionQueue->insert($action, 1);

        $this->parser->shouldReceive('setFileName')->with($fileName)->once();
        $this->parser->shouldReceive('preProcess')->once();
        $this->parser->shouldReceive('getActions')->once()->andReturn($actionQueue);
        $this->parser->shouldReceive('hasErrors')->once()->andReturn(false);

        $this->importer->setFileName($fileName);
        $this->importer->setGroup($this->school);
        $this->importer->perform();

        $this->assertEquals(3, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.run',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[1]
        );

        $this->assertEquals(
            [
                'name'   => 'nyc.import.excel.complete',
                'target' => $this->parser,
                'params' => [],
            ],
            $this->calledEvents[2]
        );
    }
}
