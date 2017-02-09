<?php

namespace IntegrationTest\Service;

use Group\Group;
use Group\Service\UserGroupServiceInterface;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use User\UserInterface;
use Zend\Paginator\Paginator;

/**
 * Test UserGroupServiceTest
 *
 * @group Friend
 * @group IntegrationTest
 * @group FriendService
 * @group UserGroupService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserGroupServiceTest extends TestCase
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/users.dataset.php');
    }

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->userGroupService = TestHelper::getDbServiceManager()->get(UserGroupServiceInterface::class);
    }

    /**
     * @dataProvider groupUsersDataProvider
     */
    public function testItShouldReturnAllUsersForGroupDescendingDown($groupId, $organizationId, array $expectedIds)
    {
        $group = new Group();
        $group->setGroupId($groupId);
        $group->setOrganizationId($organizationId);

        $userPage  = new Paginator($this->userGroupService->fetchUsersForGroup($group));
        $actualIds = [];
        /** @var UserInterface $user */
        foreach ($userPage as $user) {
            $this->assertInstanceOf(UserInterface::class, $user);
            array_push($actualIds, $user->getUserId());
        }

        sort($actualIds);
        sort($expectedIds);

        $this->assertEquals($expectedIds, $actualIds, 'Service did not return the correct users from the database');
    }

    /**
     * @dataProvider orgUsersDataProvider
     */
    public function testItShouldReturnAllUsersForOrg($organizationId, array $expectedIds)
    {
        $userPage  = new Paginator($this->userGroupService->fetchUsersForOrg($organizationId));
        $actualIds = [];
        /** @var UserInterface $user */
        foreach ($userPage as $user) {
            $this->assertInstanceOf(UserInterface::class, $user);
            array_push($actualIds, $user->getUserId());
        }

        sort($actualIds);
        sort($expectedIds);

        $this->assertEquals($expectedIds, $actualIds, 'Service did not return the correct users from the database');
    }

    /**
     * @dataProvider userRelationshipProvider
     */
    public function testItShouldFetchAllUsersThatUserHasRelationshipWith($user, array $expectedIds)
    {
        $userPage = new Paginator($this->userGroupService->fetchAllUsersForUser($user));
        $actualIds = [];
        /** @var UserInterface $user */
        foreach ($userPage as $user) {
            $this->assertInstanceOf(UserInterface::class, $user);
            array_push($actualIds, $user->getUserId());
        }

        sort($actualIds);
        sort($expectedIds);

        $this->assertEquals($expectedIds, $actualIds, 'Service did not return the correct users from the database');
    }

    /**
     * @return array
     */
    public function userRelationshipProvider()
    {
        return [
            'Principal' => [
                'user'              => 'principal',
                'expected_user_ids' => [
                    'math_teacher',
                    'math_student',
                    'english_teacher',
                    'english_student',
                ],
            ],

            'English Teacher' => [
                'user'              => 'english_teacher',
                'expected_user_ids' => [
                    'english_student',
                    'principal',
                ],
            ],

            'Math Teacher' => [
                'user'              => 'math_teacher',
                'expected_user_ids' => [
                    'math_student',
                    'principal',
                ],
            ],

            'English Student' => [
                'user'              => 'english_student',
                'expected_user_ids' => [
                    'english_teacher',
                    'principal',
                ],
            ],

            'Math Student' => [
                'user'              => 'math_student',
                'expected_user_ids' => [
                    'math_teacher',
                    'principal',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function orgUsersDataProvider()
    {
        return [
            'Gina\'s District' => [
                'organization_id'   => 'district',
                'expected_user_ids' => [
                    'math_teacher',
                    'math_student',
                    'english_teacher',
                    'english_student',
                    'principal',
                ],
            ],

            'MANCHUCK\'s District' => [
                'organization_id'   => 'manchuck',
                'expected_user_ids' => [
                    'other_teacher',
                    'other_student',
                    'other_principal',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function groupUsersDataProvider()
    {
        return [
            'English Class' => [
                'group_id'          => 'english',
                'organization_id'   => 'district',
                'expected_user_ids' => [
                    'english_teacher',
                    'english_student',
                ],
            ],

            'Math Class' => [
                'group_id'          => 'math',
                'organization_id'   => 'district',
                'expected_user_ids' => [
                    'math_teacher',
                    'math_student',
                ],
            ],

            'School' => [
                'group_id'          => 'school',
                'organization_id'   => 'district',
                'expected_user_ids' => [
                    'math_teacher',
                    'math_student',
                    'english_teacher',
                    'english_student',
                    'principal',
                ],
            ],
        ];
    }
}
