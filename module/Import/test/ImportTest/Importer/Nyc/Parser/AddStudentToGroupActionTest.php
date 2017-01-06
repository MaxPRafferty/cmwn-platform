<?php

namespace ImportTest\Importer\Nyc\Parser;

use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Parser\AddStudentToGroupAction;
use Import\Importer\Nyc\Students\Student;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;

/**
 * Test AddStudentToGroupTest
 *
 * @group Import
 * @group User
 * @group Action
 * @group Group
 * @group ClassRoom
 */
class AddStudentToGroupActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Group
     */
    protected $classRoomGroup;

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * @var \Mockery\MockInterface|\Group\Service\UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var Child
     */
    protected $user;

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
        $this->classRoom = new ClassRoom('History of the world', 'hist101', []);
        $this->classRoom->setGroup($this->classRoomGroup);
    }

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
    public function setUpStudentUser()
    {
        $this->user = new Child;
    }

    /**
     * @return Student
     */
    protected function getGoodStudent()
    {
        $student = new Student();
        $student->setFirstName('Chuck');
        $student->setLastName('Reeves');
        $student->setBirthday(new \DateTime('1982-05-13 23:38:00'));
        $student->setStudentId('foo-bar');
        $student->setUser($this->user);
        $student->setClassRoom($this->classRoom);

        return $student;
    }

    /**
     * @test
     */
    public function testItShouldReportCorrectAction()
    {
        $action = new AddStudentToGroupAction($this->getGoodStudent(), $this->userGroupService);

        $this->assertEquals(
            'Adding student "foo-bar" to Classroom [hist101] "History of the world"',
            $action->__toString(),
            'AddStudentToGroupAction reporting invalid command'
        );
    }

    /**
     * @test
     */
    public function testItShouldSaveStudentToGroup()
    {
        $this->userGroupService->shouldReceive('attachUserToGroup')
            ->with($this->classRoomGroup, $this->user, 'student')
            ->once();

        $action = new AddStudentToGroupAction($this->getGoodStudent(), $this->userGroupService);
        $this->assertEquals(
            5,
            $action->priority(),
            'AddStudentToGroupAction has invalid priority'
        );

        $action->execute();
    }
}
