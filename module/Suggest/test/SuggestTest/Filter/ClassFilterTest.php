<?php

namespace SuggestTest\Filter;

use Group\Group;
use Group\Service\UserGroupServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Suggest\Filter\ClassFilter;
use Suggest\SuggestionCollection;
use User\Adult;
use User\Child;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class ClassFilterTest
 *
 * @group Group
 * @group Suggest
 * @group User
 */
class ClassFilterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ClassFilter
     */
    protected $classFilter;

    /**
     * @var \Mockery\MockInterface|UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @before
     */
    public function setUpClassFilter()
    {
        $this->classFilter = new ClassFilter($this->userGroupService);
    }

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->userGroupService = \Mockery::mock(UserGroupServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldReturnUserFromOneGroup()
    {
        $student    = new Child(['user_id' => 'english_student']);
        $teacher    = new Adult(['user_id' => 'english_teacher']);
        $paginator  = \Mockery::mock(DbSelect::class);
        $group      = new Group();
        $collection = new SuggestionCollection();

        $this->userGroupService->shouldReceive("fetchGroupsForUser")
            ->andReturn($paginator)
            ->once();

        $paginator->shouldReceive('getItems')
            ->andReturn([$group])
            ->once();

        $this->userGroupService->shouldReceive('fetchUsersForGroup')
            ->andReturn($paginator)
            ->once();

        $paginator->shouldReceive('getItems')
            ->andReturn([$student, $teacher]);

        $this->classFilter->getSuggestions($collection, $student);

        $this->assertEquals(
            ['english_student' => $student, 'english_teacher' => $teacher],
            $collection->getArrayCopy(),
            'Incorrect suggestions returned'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnUserFromMultipleGroups()
    {
        $student = new Child(['user_id' => 'english_student']);
        $groups  = $this->getUserGroups(5);
        $users   = $this->getUsersForGroups($groups, 5);

        $groupResult = \Mockery::mock(DbSelect::class);
        $groupResult->shouldReceive('getItems')
            ->andReturn($groups);

        $this->userGroupService->shouldReceive('fetchGroupsForUser')
            ->andReturn($groupResult)
            ->ordered('user_friend');

        foreach ($groups as $group) {
            $groupUserResult = new ArrayAdapter($users[$group->getGroupId()]);
            $this->userGroupService->shouldReceive('fetchUsersForGroup')
                ->with($group)
                ->andReturn($groupUserResult)
                ->ordered('user_friends');
        }

        $collection  = new SuggestionCollection();
        $classFilter = new ClassFilter($this->userGroupService, 5, 5);
        $classFilter->getSuggestions($collection, $student);
        $this->assertEquals(
            [
                'group_0_user_0',
                'group_0_user_1',
                'group_0_user_2',
                'group_0_user_3',
                'group_0_user_4',
                'group_1_user_0',
                'group_1_user_1',
                'group_1_user_2',
                'group_1_user_3',
                'group_1_user_4',
                'group_2_user_0',
                'group_2_user_1',
                'group_2_user_2',
                'group_2_user_3',
                'group_2_user_4',
                'group_3_user_0',
                'group_3_user_1',
                'group_3_user_2',
                'group_3_user_3',
                'group_3_user_4',
                'group_4_user_0',
                'group_4_user_1',
                'group_4_user_2',
                'group_4_user_3',
                'group_4_user_4',
            ],
            array_keys($collection->getArrayCopy()),
            'Incorrect suggestions returned'
        );
    }

    /**
     * @param $number
     *
     * @return Group[]
     */
    protected function getUserGroups($number)
    {
        $groups = [];
        for ($groupCount = 0; $groupCount < $number; $groupCount++) {
            $group = new Group();
            $group->setGroupId('group_' . $groupCount);
            array_push($groups, $group);
        }

        return $groups;
    }

    /**
     * @param Group[] $groups
     * @param int $usersPerGroup
     *
     * @return array
     */
    protected function getUsersForGroups(array $groups, $usersPerGroup)
    {
        $users = [];
        array_walk($groups, function (Group $group) use (&$users, &$usersPerGroup) {
            $users[$group->getGroupId()] = [];
            for ($userCount = 0; $userCount < $usersPerGroup; $userCount++) {
                array_push(
                    $users[$group->getGroupId()],
                    new Child(['user_id' => $group->getGroupId() . '_user_' . $userCount])
                );
            }
        });

        return $users;
    }
}
