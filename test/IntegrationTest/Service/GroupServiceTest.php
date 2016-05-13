<?php

namespace IntegrationTest\Service;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;
use Zend\Paginator\Paginator;

/**
 * Exception GroupServiceTest
 *
 * @group Group
 * @group IntegrationTest
 * @group GroupService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupServiceTest extends TestCase
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->groupService = TestHelper::getServiceManager()->get(GroupServiceInterface::class);
    }

    /**
     * @param $user
     * @param $expectedGroups
     * @dataProvider userGroupDataProvider
     */
    public function testItShouldFetchAllForUser($user, array $expectedGroups)
    {
        $groups       = $this->groupService->fetchAllForUser($user);
        $groupsPage   = new Paginator($groups);
        $actualGroups = [];
        /** @var GroupInterface $group */
        foreach ($groupsPage as $group) {
            $this->assertInstanceOf(GroupInterface::class, $group);
            array_push($actualGroups, $group->getGroupId());
        }

        sort($actualGroups);
        sort($expectedGroups);

        $this->assertSame($expectedGroups, $actualGroups, 'Fetching groups for a user did not return correct groups');
    }

    /**
     * @dataProvider userGroupTypeDataProvider
     */
    public function testItShouldFetchAllForUserWithType($user, $type, array $expectedGroups)
    {
        $where        = new Where();
        $where->addPredicate(new Operator('g.type', '=', $type));
        $groups       = $this->groupService->fetchAllForUser($user, $where);
        $groupsPage   = new Paginator($groups);

        $actualGroups = [];
        /** @var GroupInterface $group */
        foreach ($groupsPage as $group) {
            $this->assertInstanceOf(GroupInterface::class, $group);
            array_push($actualGroups, $group->getGroupId());
        }

        sort($actualGroups);
        sort($expectedGroups);

        $this->assertSame($expectedGroups, $actualGroups, 'Fetching groups for a user did not return correct groups');
    }

    /**
     * @return array
     */
    public function userGroupDataProvider()
    {
        return [
            'Principal' => [
                'user'            => 'principal',
                'expected_groups' => [
                    'english',
                    'math',
                    'school',
                ],
            ],

            'English Teacher' => [
                'user'            => 'english_teacher',
                'expected_groups' => [
                    'english',
                ],
            ],

            'Math Teacher' => [
                'user'            => 'math_teacher',
                'expected_groups' => [
                    'math',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function userGroupTypeDataProvider()
    {
        return [
            'Principal with Class' => [
                'user'            => 'principal',
                'type'            => 'class',
                'expected_groups' => [
                    'english',
                    'math',
                ],
            ],

            'Principal with School' => [
                'user'            => 'principal',
                'type'            => 'school',
                'expected_groups' => [
                    'school',
                ],
            ],

            'Principal with foo' => [
                'user'            => 'principal',
                'type'            => 'foo',
                'expected_groups' => [
                ],
            ],

            'English Teacher with Class' => [
                'user'            => 'english_teacher',
                'type'            => 'class',
                'expected_groups' => [
                    'english',
                ],
            ],

            'English Teacher with School' => [
                'user'            => 'english_teacher',
                'type'            => 'school',
                'expected_groups' => [
                ],
            ],

            'English Teacher with foo' => [
                'user'            => 'english_teacher',
                'type'            => 'foo',
                'expected_groups' => [
                ],
            ],

            'Math Teacher with Class' => [
                'user'            => 'math_teacher',
                'type'            => 'class',
                'expected_groups' => [
                    'math',
                ],
            ],

            'Math Teacher with School' => [
                'user'            => 'math_teacher',
                'type'            => 'school',
                'expected_groups' => [
                ],
            ],

            'Math Teacher with foo' => [
                'user'            => 'math_teacher',
                'type'            => 'foo',
                'expected_groups' => [
                ],
            ],
        ];
    }
}
