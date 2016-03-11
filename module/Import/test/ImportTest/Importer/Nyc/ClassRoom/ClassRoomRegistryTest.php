<?php

namespace ImportTest\Importer\Nyc\ClassRoom;

use Application\Exception\NotFoundException;
use Group\Group;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception ClassRoomRegistryTest
 *
 * ${CARET}
 */
class ClassRoomRegistryTest extends TestCase
{
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
    }

    /**
     * @before
     */
    public function setUpRegistry()
    {
        $this->registry = new ClassRoomRegistry($this->groupService);
    }

    public function testItShouldLookInLocalRegistryBeforeQueryingTheDatabase()
    {
        $classroom = new ClassRoom('History of the world', 'hist101');
        $this->registry->addClassroom($classroom);

        $this->groupService->shouldNotReceive('fetchGroup');

        $this->assertTrue($this->registry->offsetExists('hist101'));
    }

    public function testItShouldConvertGroupToClassRoomWhenSearching()
    {
        $group = new Group();
        $group->setTitle('History of the world');
        $group->setExternalId('hist101');
        $group->setMeta(['sub_classes' => ['foo', 'bar']]);

        $this->groupService->shouldReceive('fetchGroup')
            ->with('hist101')
            ->andReturn($group)
            ->once();

        $this->assertTrue($this->registry->offsetExists('hist101'));
    }

    public function testItShouldReturnFalseWhenDbLookFailsToFindClass()
    {
        $this->groupService->shouldReceive('fetchGroup')
            ->with('hist101')
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertfalse($this->registry->offsetExists('hist101'));
    }

    public function testItShouldUseIdFromClassRoomForOffsetSet()
    {
        $classroom = new ClassRoom('History of the world', 'hist101');
        $this->registry->offsetSet('foobar', $classroom);

        $this->groupService->shouldNotReceive('fetchGroup');

        $this->assertSame($classroom, $this->registry->offsetGet('hist101'));
    }

    public function testItShouldReturnNullWhenNotSet()
    {
        $this->groupService->shouldReceive('fetchGroup')
            ->with('hist101')
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertNull($this->registry->offsetGet('hist101'));
    }

    public function testItShouldThrowBadMethodCallExceptionOnUnset()
    {
        $this->setExpectedException(
            \BadmethodCallException::class,
            'Cannot unset values from the Classroom Registry'
        );

        $this->registry->offsetUnset('foo');
    }
}
