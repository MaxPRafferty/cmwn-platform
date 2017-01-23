<?php

namespace ImportTest\Importer\Nyc\ClassRoom;

use Group\GroupInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\AddClassRoomAction;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test ClassRoomActionTest
 *
 * @group Import
 * @group Group
 * @group ClassRoom
 * @group NycImport
 * @group Service
 * @group GroupService
 */
class ClassRoomActionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = \Mockery::mock('\Group\Service\GroupService');
    }

    /**
     * @test
     */
    public function testItShouldReportCorrectName()
    {
        $classRoom = new ClassRoom('History of the world', 'hist101', ['hist001']);

        $this->assertTrue(
            $classRoom->isNew(),
            'Update this test to account for new login in class room'
        );

        $action = new AddClassRoomAction($this->groupService, $classRoom);

        $this->assertEquals(
            'Creating new class room [hist101] "History of the world"',
            $action->__toString(),
            'AddClassRoomAction has incorrect to string'
        );
    }

    /**
     * @test
     */
    public function testItShouldSaveClassRoomAndSetGroup()
    {
        $classRoom = new ClassRoom('History of the world', 'hist101', ['hist001']);

        $this->assertTrue(
            $classRoom->isNew(),
            'Update this test to account for new login in class room'
        );

        $action = new AddClassRoomAction($this->groupService, $classRoom);

        $this->assertEquals(100, $action->priority(), 'Priority for class room has changed');
        $this->groupService->shouldReceive('createGroup')
            ->once()
            ->andReturnUsing(function (GroupInterface $group) {
                $this->assertEquals(
                    'hist101',
                    $group->getExternalId(),
                    'ClassRoomAction did not use the classroom Id for the group external id'
                );

                $this->assertEquals(
                    'History of the world',
                    $group->getTitle(),
                    'ClassRoomAction did not use the classroom name for the group title'
                );

                $this->assertEquals(
                    ['sub_class_rooms' => ['hist001']],
                    $group->getMeta(),
                    'ClassRoomAction did not set the sub classes to meta data correctly'
                );

                $group->setExternalId('foo-bar');

                return true;
            });

        $action->execute();

        $this->assertFalse(
            $classRoom->isNew(),
            'Class room MUST NOT be new after executing the action'
        );
    }
}
