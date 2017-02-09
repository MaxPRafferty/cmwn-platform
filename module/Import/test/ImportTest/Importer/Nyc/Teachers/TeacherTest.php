<?php

namespace ImportTest\Importer\Nyc\Teachers;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Exception\InvalidTeacherException;
use Import\Importer\Nyc\Teachers\Teacher;
use PHPUnit\Framework\TestCase as TestCase;
use User\Adult;
use User\Child;

/**
 * Test TeacherTest
 *
 * @group Import
 * @group User
 * @group Teacher
 * @group NycImport
 */
class TeacherTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldValidateOnCorrectTeacher()
    {
        $teacher = new Teacher();
        $teacher->setFirstName('Chuck');
        $teacher->setLastName('Reeves');
        $teacher->setEmail('chuck@manchuck.com');
        $teacher->setRole('The man');

        $this->assertTrue($teacher->isValid(), 'This teacher should be valid');
        $this->assertTrue($teacher->isNew(), 'This teacher should be new');

        $teacher->setUser(new Adult());
        $this->assertFalse($teacher->isNew(), 'This teacher should not be new any more');

        $this->assertFalse($teacher->hasClassAssigned(), 'The teacher should not have a classroom set');

        $teacher->setClassRoom(new ClassRoom('History of the world', 'hist101'));
        $this->assertTrue($teacher->hasClassAssigned(), 'The teacher should now report as having a classroom assigned');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTryingToSetChildAsUser()
    {
        $this->expectException(InvalidTeacherException::class);
        $teacher = new Teacher();
        $teacher->setUser(new Child());
    }
}
