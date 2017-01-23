<?php

namespace ImportTest\Importer\Nyc\ClassRoom;

use Application\Exception\NotFoundException;
use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidClassRoomException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test ClassRoomRegistryTest
 *
 * @group Registry
 * @group Import
 * @group ClassRoom
 * @group Group
 * @group NycImport
 */
class ClassRoomRegistryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var ClassRoomRegistry
     */
    protected $registry;

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\GroupService');
        $this->groupService->shouldReceive('fetchGroupByExternalId')->andThrow(new NotFoundException())->byDefault();
    }

    /**
     * @before
     */
    public function setUpRegistry()
    {
        $this->registry = new ClassRoomRegistry($this->groupService);
        $this->registry->setNetworkId('foo-bar');
    }

    /**
     * @test
     */
    public function testItShouldLookInLocalRegistryBeforeQueryingTheDatabase()
    {
        $classroom = new ClassRoom('History of the world', 'hist101');
        $this->registry->addClassroom($classroom);

        $this->groupService->shouldNotReceive('fetchGroupByExternalId');

        $this->assertTrue($this->registry->offsetExists('hist101'));
    }

    /**
     * @test
     */
    public function testItShouldConvertGroupToClassRoomWhenSearching()
    {
        $group = new Group();
        $group->setTitle('History of the world');
        $group->setExternalId('hist101');
        $group->setMeta(['sub_classes' => ['foo', 'bar']]);

        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->with('foo-bar', 'hist101')
            ->andReturn($group)
            ->once();

        $this->assertTrue(
            $this->registry->offsetExists('hist101'),
            'Registry did not find the classroom from the database'
        );
    }

    /**
     * @test
     */
    public function testItShouldAttachGroupToExistingGroupWhenAddingClassRoom()
    {
        $classRoom = new ClassRoom('History of the world', 'hist101');
        $group     = new Group();
        $group->setTitle('History of the world');
        $group->setExternalId('hist101');
        $group->setMeta(['sub_classes' => ['foo', 'bar']]);

        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->with('foo-bar', 'hist101')
            ->andReturn($group)
            ->once();

        $this->assertTrue($classRoom->isNew(), 'Class room is not considered new anymore');
        $this->assertNull($classRoom->getGroup(), 'Class room was created with a group');

        $this->registry->addClassroom($classRoom);
        $this->assertFalse($classRoom->isNew(), 'Classroom is considered new after attaching group');
        $this->assertSame($group, $classRoom->getGroup(), 'Registry did not attach group');
    }

    /**
     * @test
     */
    public function testItShouldReturnFalseWhenDbLookFailsToFindClass()
    {
        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->with('foo-bar', 'hist101')
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertFalse($this->registry->offsetExists('hist101'));
    }

    /**
     * @test
     */
    public function testItShouldUseIdFromClassRoomForOffsetSet()
    {
        $classroom = new ClassRoom('History of the world', 'hist101');
        $this->registry->offsetSet('foobar', $classroom);

        $this->groupService->shouldNotReceive('fetchGroupByExternalId');

        $this->assertSame($classroom, $this->registry->offsetGet('hist101'));
    }

    /**
     * @test
     */
    public function testItShouldReturnNullWhenNotSet()
    {
        $this->groupService->shouldReceive('fetchGroupByExternalId')
            ->with('foo-bar', 'hist101')
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertNull($this->registry->offsetGet('hist101'));
    }

    /**
     * @test
     */
    public function testItShouldThrowBadMethodCallExceptionOnUnset()
    {
        $this->setExpectedException(
            \BadMethodCallException::class,
            'Cannot unset values from the Classroom Registry'
        );

        $this->registry->offsetUnset('foo');
    }

    /**
     * @test
     */
    public function testItShouldNotAddBadClassrooms()
    {
        $classRoom = new ClassRoom('', '');
        $this->assertFalse($classRoom->isValid(), 'Unable to crate invalid classroom');
        $this->setExpectedException(
            InvalidClassRoomException::class,
            'Class has invalid keys'
        );

        $this->registry->addClassroom($classRoom);
    }
}
