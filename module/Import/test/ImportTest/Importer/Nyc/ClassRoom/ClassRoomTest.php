<?php

namespace ImportTest\Importer\Nyc\ClassRoom;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception ClassRoomTest
 */
class ClassRoomTest extends TestCase
{
    public function testItShouldValidateCorrectClassRoom()
    {
        $classRoom = new ClassRoom('History of the world', 'history 101', []);
        $this->assertTrue($classRoom->isValid(), 'Classroom was not validated');
        $this->assertFalse($classRoom->hasSubClasses(), 'Classroom dosent have subclasses');

        $classRoom->setSubClassRooms(['foo']);
        $this->assertTrue($classRoom->hasSubClasses(), 'Classroom should now have ');
    }

    public function testItShouldValidateFalseWithEmptyTitle()
    {
        $classRoom = new ClassRoom('', 'history 101', []);
        $this->assertFalse($classRoom->isValid(), 'Classroom should be invalid with empty title');
    }

    public function testItShouldValidateFalseWithEmptyId()
    {
        $classRoom = new ClassRoom('History of the world', '', []);
        $this->assertFalse($classRoom->isValid(), 'Classroom should be invalid with empty id');
    }
 
    public function testItShouldValidateFalseWithEmptyTitleAndId()
    {
        $classRoom = new ClassRoom('', '', []);
        $this->assertFalse($classRoom->isValid(), 'Classroom should be invalid with empty id and title');
    }
}
