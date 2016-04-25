<?php

namespace ImportTest\Importer\Nyc\Students;

use Application\Exception\NotFoundException;
use Import\Importer\Nyc\Exception\InvalidStudentException;
use Import\Importer\Nyc\Students\Student;
use Import\Importer\Nyc\Students\StudentRegistry;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Child;

/**
 * Test StudentRegistryTest
 *
 * @group Student
 * @group Registry
 * @group User
 * @group Import
 * @group NycImport
 */
class StudentRegistryTest extends TestCase
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
        $this->service->shouldReceive('fetchUserByExternalId')
            ->andThrow(new NotFoundException)
            ->byDefault();
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

    /**
     * @test
     */
    public function testItShouldShouldLookInLocalStorageBeforeQueryingTheDatabase()
    {
        $student = $this->getGoodStudent();
        $student->setUser(new Child());
        $this->registry->offsetSet('foo-bar', $student);
        $this->assertTrue($this->registry->offsetExists('foo-bar'));
        $this->assertSame($student, $this->registry->offsetGet('foo-bar'));
    }

    /**
     * @test
     */
    public function testItShouldConvertGroupToClassRoomWhenSearching()
    {
        $user = new Child();
        $user->setExternalId('foo-bar');
        $user->setFirstName('Chuck');
        $user->setLastName('Reeves');
        $user->setBirthdate(new \DateTime('1982-05-13 23:38:00'));

        $this->service->shouldReceive('fetchUserByExternalId')
            ->once()
            ->andReturn($user);

        $this->assertTrue(
            $this->registry->offsetExists('foo-bar'),
            'Registry did not find the user from the database'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachUserWhenAddingExistingStudent()
    {
        $user = new Child();
        $user->setExternalId('foo-bar');
        $user->setFirstName('Chuck');
        $user->setLastName('Reeves');

        $student = $this->getGoodStudent();
        $this->assertNull($student->getUser(), 'Student was created with a user');

        $this->service->shouldReceive('fetchUserByExternalId')
            ->once()
            ->andReturn($user);

        $this->registry->addStudent($student);

        $this->assertSame($user, $student->getUser(), 'User was not attached to student');
    }

    /**
     * @test
     */
    public function testItShouldReturnFalseAndNullWhenUserNotFoundAndStudentNotSet()
    {
        $this->assertFalse($this->registry->offsetExists('foo'));
        $this->assertNull($this->registry->offsetGet('foo'));
    }

    /**
     * @test
     */
    public function testItShouldThrowBadMethodCallExceptionOnUnset()
    {
        $this->setExpectedException(
            \BadMethodCallException::class,
            'Cannot unset values from the Student Registry'
        );

        $this->registry->offsetUnset('foo');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenAddingBadStudent()
    {
        $student = new Student();
        $this->assertFalse($student->isValid(), 'I do not know how to make an invalid student any more');
        $this->setExpectedException(
            InvalidStudentException::class,
            'Student has invalid keys'
        );
        $this->registry->addStudent($student);
    }
}
