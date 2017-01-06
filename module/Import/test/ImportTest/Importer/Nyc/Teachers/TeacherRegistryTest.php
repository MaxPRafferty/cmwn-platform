<?php

namespace ImportTest\Importer\Nyc\Teachers;

use Application\Exception\NotFoundException;
use Import\Importer\Nyc\Exception\InvalidTeacherException;
use Import\Importer\Nyc\Teachers\Teacher;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;

/**
 * Test TeacherRegistryTest
 *
 * @group User
 * @group Teacher
 * @group Import
 * @group NycImport
 * @group Registry
 */
class TeacherRegistryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var TeacherRegistry
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
        $this->service->shouldReceive('fetchUserByEmail')
            ->andThrow(new NotFoundException())
            ->byDefault();
    }

    /**
     * @before
     */
    public function setUpRegistry()
    {
        $this->registry = new TeacherRegistry($this->service);
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

        return $teacher;
    }

    /**
     * @throws InvalidTeacherException
     * @test
     */
    public function testItShouldShouldLookInLocalStorageBeforeQueryingTheDatabase()
    {
        $teacher = $this->getGoodTeacher();
        $teacher->setUser(new Adult());
        $this->registry->offsetSet('chuck@manchuck.cpm', $teacher);
        $this->assertTrue($this->registry->offsetExists('chuck@manchuck.com'));
        $this->assertSame($teacher, $this->registry->offsetGet('chuck@manchuck.com'));
    }

    /**
     * @test
     */
    public function testItShouldConvertGroupToClassRoomWhenSearching()
    {
        $user = new Adult();
        $user->setEmail('chuck@manchuck.com');
        $user->setFirstName('Chuck');
        $user->setLastName('Reeves');

        $this->service->shouldReceive('fetchUserByEmail')
            ->once()
            ->andReturn($user);

        $this->assertTrue(
            $this->registry->offsetExists('chuck@manchuck.com'),
            'Registry did not find the user from the database'
        );
    }

    /**
     * @throws InvalidTeacherException
     * @test
     */
    public function testItShouldAttachUserWhenAddingExistingTeacher()
    {
        $user = new Adult();
        $user->setEmail('chuck@manchuck.com');
        $user->setFirstName('Chuck');
        $user->setLastName('Reeves');

        $teacher = $this->getGoodTeacher();
        $this->assertNull($teacher->getUser(), 'Teacher was created with a user');

        $this->service->shouldReceive('fetchUserByEmail')
            ->once()
            ->andReturn($user);

        $this->registry->addTeacher($teacher);

        $this->assertSame($user, $teacher->getUser(), 'User was not attached to teacher');
    }

    /**
     * @test
     */
    public function testItShouldReturnFalseAndNullWhenUserNotFoundAndTeacherNotSet()
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
            'Cannot unset values from the Teacher Registry'
        );

        $this->registry->offsetUnset('foo');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenAddingBadTeacher()
    {
        $teacher = new Teacher();
        $this->assertFalse($teacher->isValid(), 'I do not know how to make an invalid teacher any more');
        $this->setExpectedException(
            InvalidTeacherException::class,
            'Teacher has invalid keys'
        );
        $this->registry->addTeacher($teacher);
    }
}
