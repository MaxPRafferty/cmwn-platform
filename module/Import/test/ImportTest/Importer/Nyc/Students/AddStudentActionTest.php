<?php

namespace ImportTest\Importer\Nyc\Students;

use Import\Importer\Nyc\Students\AddStudentAction;
use Import\Importer\Nyc\Students\Student;
use Import\Importer\Nyc\Students\StudentRegistry;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;


/**
 * Exception AddStudentActionTest
 */
class AddStudentActionTest extends TestCase
{
    /**
     * @var StudentRegistry
     */
    protected $registry;

    /**
     * @var \Mockery\MockInterface|\User\Service\UserServiceInterface
     */
    protected $service;

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->service = \Mockery::mock('\User\Service\UserServiceInterface');
    }

    /**
     * @before
     */
    public function setUpRegistry()
    {
        $this->registry = new StudentRegistry($this->service);
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

        return $student;
    }
    
    public function testItShouldReportCorrectName()
    {
        $student = $this->getGoodStudent();
        $action = new AddStudentAction($this->service, $student);

        $this->assertEquals(
            'Creating user for a student with id foo-bar',
            $action->__toString(),
            'AddStudentAction has the wrong toString'
        );
    }

    public function testItShouldSaveStudentToDataBase()
    {
        $student = $this->getGoodStudent();

        $this->assertTrue(
            $student->isNew(),
            'Update this test for new student standards'
        );

        $action = new AddStudentAction($this->service, $student);

        $this->assertEquals(20, $action->priority(), 'Priority for student has changed');
        $this->service->shouldReceive('createUser')
            ->once()
            ->andReturnUsing(function (Child $user) {
                $this->assertEquals(
                    'Chuck',
                    $user->getFirstName(),
                    'Action did not map first name correctly'
                );

                $this->assertEquals(
                    'Reeves',
                    $user->getLastName(),
                    'Action did not map last name correcrtly'
                );

                return true;
            });

        $action->execute();
        $this->assertInstanceOf('\User\Child', $student->getUser(), 'Action did not add user to student®');
    }
}
