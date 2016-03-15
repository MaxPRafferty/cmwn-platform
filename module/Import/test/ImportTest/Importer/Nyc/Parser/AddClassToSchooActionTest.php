<?php

namespace ImportTest\Importer\Nyc\Parser;

use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Parser\AddClassToSchooAction;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception AddClassToSchooActionTest
 *
 * ${CARET}
 */
class AddClassToSchooActionTest extends TestCase
{
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
    public function setUpGroupService()
    {
        /** @var  $this->groupService */
        $this->groupService = \Mockery::mock('\Group\Service\GroupServiceInterface');
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
    public function setUpClassRoom()
    {
        $this->classRoom = new ClassRoom('History of the world', 'history 101', []);
        $this->classRoom->setGroup($this->classRoomGroup);
    }

    /**
     * @before
     */
    public function setUpSchool()
    {
        $this->school = new Group();
        $this->school->setTitle('MANCHUCK School of Rock');
    }

    public function testItShouldReportCorrectCommand()
    {
        $action = new AddClassToSchooAction($this->school, $this->classRoom, $this->groupService);
        $this->assertEquals(
            'Adding class room "History of the world" to school "MANCHUCK School of Rock"',
            $action->__toString(),
            'AddClassToSchooAction is not reporting correct command'
        );
    }

    public function testItShouldAddChildToGroup()
    {
        $this->groupService->shouldReceive('addChildToGroup')
            ->once()
            ->with($this->school, $this->classRoomGroup);

        $action = new AddClassToSchooAction($this->school, $this->classRoom, $this->groupService);
        $this->assertEquals(1, $action->priority(), 'AddClassToSchoolAction has incorrect priority');
        $action->execute();
    }
}
