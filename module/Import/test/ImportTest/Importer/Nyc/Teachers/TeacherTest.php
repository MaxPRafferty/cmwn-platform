<?php

namespace ImportTest\Importer\Nyc\Teachers;

use Import\Importer\Nyc\Teachers\Teacher;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;

/**
 * Exception TeacherTest
 *
 * @todo expand tests
 */
class TeacherTest extends TestCase
{
    public function testItShouldValidateOnCorrectTeacher()
    {
        $teacher = new Teacher();
        $teacher->setFirstName('Chuck');
        $teacher->setLastName('Reeves');
        $teacher->setEmail('chuck@manchuck.com');
        $teacher->setRole('The man');

        $this->assertTrue($teacher->isValid(), 'This teacher should be valid');
    }

    public function testItShouldReportNewTeacherWhenUserNotSet()
    {
        $teacher = new Teacher();
        $teacher->setFirstName('Chuck');
        $teacher->setLastName('Reeves');
        $teacher->setEmail('chuck@manchuck.com');
        $teacher->setRole('The man');
        $this->assertTrue($teacher->isNew(), 'This teacher should be new');

        $teacher->setUser(new Adult());
        $this->assertFalse($teacher->isNew(), 'This teacher should not be new any more');

    }

}
