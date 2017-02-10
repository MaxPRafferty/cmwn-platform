<?php

namespace ImportTest\Importer\Nyc\ClassRoom;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test ClassRoomTest
 *
 * @group Import
 * @group Group
 * @group ClassRoom
 * @group NycImport
 */
class ClassRoomTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldValidateCorrectClassRoom()
    {
        $classRoom = new ClassRoom('History of the world', 'history 101', []);
        $this->assertTrue($classRoom->isValid(), 'Classroom was not validated');
        $this->assertFalse($classRoom->hasSubClasses(), 'Classroom dose\'nt have subclasses');

        $classRoom->setSubClassRooms(['foo']);
        $this->assertTrue($classRoom->hasSubClasses(), 'Classroom should now have ');
    }

    /**
     * @test
     */
    public function testItShouldValidateFalseWithEmptyTitle()
    {
        $classRoom = new ClassRoom('', 'history 101', []);
        $this->assertFalse($classRoom->isValid(), 'Classroom should be invalid with empty title');
    }

    /**
     * @test
     */
    public function testItShouldValidateFalseWithEmptyId()
    {
        $classRoom = new ClassRoom('History of the world', '', []);
        $this->assertFalse($classRoom->isValid(), 'Classroom should be invalid with empty id');
    }

    /**
     * @test
     */
    public function testItShouldValidateFalseWithEmptyTitleAndId()
    {
        $classRoom = new ClassRoom('', '', []);
        $this->assertFalse($classRoom->isValid(), 'Classroom should be invalid with empty id and title');
    }
}
