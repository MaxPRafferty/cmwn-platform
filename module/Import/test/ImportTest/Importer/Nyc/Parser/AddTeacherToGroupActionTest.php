<?php

namespace ImportTest\Importer\Nyc\Parser;


use Application\Exception\NotFoundException;
use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Parser\AddTeacherToGroupAction;
use Import\Importer\Nyc\Teachers\AddTeacherAction;
use Import\Importer\Nyc\Teachers\Teacher;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;

/**
 * Exception AddTeacherToGroupActionTest
 *
 * ${CARET}
 */
class AddTeacherToGroupActionTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Group\Service\UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var Group
     */
    protected $classRoomGroup;

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * @var Adult
     */
    protected $teacherUser;

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
    public function setUpClassRoomGroup()
    {
        $this->classRoomGroup = new Group();
        $this->classRoomGroup->setType('class');
    }

    /**
     * @before
     */
    public function setUpClassRoom()
    {
        $this->classRoom = new ClassRoom('History of the world', 'hist101', []);
        $this->classRoom->setGroup($this->classRoomGroup);
    }

    /**
     * @before
     */
    public function setUpTeacherUser()
    {
        $this->teacherUser = new Adult();
    }

    /**
     * @return Teacher
     */
    protected function getGoodTeacher()
    {
        $teacher = new Teacher();
        $teacher->setFirstName('Chuck');
        $teacher->setLastName('Reeves');
        $teacher->setEmail('chuck@manchuck.com');
        $teacher->setRole('The man');
        $teacher->setClassRoom($this->classRoom);

        return $teacher;
    }

    public function testItShouldReportCorrectAction()
    {
        $teacher = $this->getGoodTeacher();
        $action = new AddTeacherToGroupAction($teacher, $this->userGroupService);

        $this->assertEquals(
            'Adding The man chuck@manchuck.com to class [hist101] "History of the world"',
            $action->__toString(),
            'Add Teacher action reported incorrect command'
        );
    }

    public function testItShouldExecuteAction()
    {
        $teacher = $this->getGoodTeacher();
        $action = new AddTeacherToGroupAction($teacher, $this->userGroupService);

        $this->assertEquals(10, $action->priority(), 'AddTeacherToGroupAction has incorrect priority');
        $this->userGroupService->shouldReceive()
            ->with($this->classRoomGroup, $this->teacherUser, 'The man')
            ->once();
    }
}
