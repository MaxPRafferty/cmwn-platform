<?php

namespace ImportTest\Importer\Nyc\Parser;

use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Parser\AddClassToSchoolAction;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test AddClassToSchoolActionTest
 *
 * @group Import
 * @group NycImport
 * @group Action
 * @group ClassRoom
 * @group Group
 */
class AddClassToSchoolActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * @var Group
     */
    protected $school;

    /**
     * @var Group
     */
    protected $classRoomGroup;

    /**
     * @before
     */
    public function setUpClassRoom()
    {
        $this->classRoom = new ClassRoom('History of the world', 'history 101', []);
        $this->classRoom->setGroup($this->classRoomGroup);
    }

    /**
     * @before
     */
    public function setUpClassRoomGroup()
    {
        $this->classRoomGroup = new Group();
    }

    /**
     * @before
     */
    public function setUpGroupService()
    {
        /** @var  $this ->groupService */
        $this->groupService = \Mockery::mock('\Group\Service\GroupServiceInterface');
    }

    /**
     * @before
     */
    public function setUpSchool()
    {
        $this->school = new Group();
        $this->school->setTitle('MANCHUCK School of Rock');
    }

    /**
     * @test
     */
    public function testItShouldReportCorrectCommand()
    {
        $action = new AddClassToSchoolAction($this->school, $this->classRoom, $this->groupService);
        $this->assertEquals(
            'Adding class room "History of the world" to school "MANCHUCK School of Rock"',
            $action->__toString(),
            'AddClassToSchoolAction is not reporting correct command'
        );
    }

    /**
     * @test
     */
    public function testItShouldattachChildToGroup()
    {
        $this->groupService->shouldReceive('addChildToGroup')
            ->once()
            ->with($this->school, $this->classRoomGroup);

        $action = new AddClassToSchoolAction($this->school, $this->classRoom, $this->groupService);
        $this->assertEquals(1, $action->priority(), 'AddClassToSchoolAction has incorrect priority');
        $action->execute();
    }
}
