<?php

namespace SuggestTest\Filter;

use Group\Service\UserGroupServiceInterface;
use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Filter\ClassFilter;
use User\Adult;
use User\Child;

/**
 * Class ClassFilterTest
 * @package SuggestTest\Rule
 */
class ClassFilterUnitTest extends TestCase
{

    /**
     * @var ClassFilter
     */
    protected $classFilter;

    /**
     * @var UserGroupServiceInterface
     */
    protected $groupService;

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = \Mockery::mock('Group\Service\UserGroupService');
    }

    /**
     * @before
     */
    public function setUpClassFilter()
    {
        $this->classFilter = new ClassFilter($this->groupService);
    }

    /**
     * @test
     */
    public function testItShouldReturnUsersFromHisGroup()
    {
        $user = new Child(['user_id' => 'english_student']);
        $suggest = new Adult(['user_id' => 'english_teacher']);
        $paginator = \Mockery::mock('Zend\Paginator\Paginator');
        $group = \Mockery::mock('Group\Group');

        $this->groupService->shouldReceive("fetchGroupsForUser")
            ->andReturn($paginator)
            ->once();
        $paginator->shouldReceive('getItems')
            ->andReturn([$group])
            ->once();
        $this->groupService->shouldReceive('fetchUsersForGroup')
            ->andReturn($paginator)
            ->once();
        $paginator->shouldReceive('getItems')
            ->andReturn([$suggest]);
        $container = $this->classFilter->getSuggestions($user);

        $actualId = [];
        foreach ($container as $suggestion) {
            $actualId[] = $suggestion->getUserId();
        }

        $expected = ["english_teacher"];
        $this->assertEquals($actualId, $expected);
    }
}
