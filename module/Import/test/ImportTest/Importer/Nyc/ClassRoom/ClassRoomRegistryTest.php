<?php

namespace ImportTest\Importer\Nyc\ClassRoom;

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
            ->andReturn($group);

        
    }
}
