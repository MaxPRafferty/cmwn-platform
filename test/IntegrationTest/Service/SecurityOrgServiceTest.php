<?php

namespace IntegrationTest\Service;

use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use Security\Service\SecurityOrgServiceInterface;
use User\Adult;

/**
 * Test SecurityOrgServiceTest
 *
 * @group Db
 * @group Security
 * @group Organization
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SecurityOrgServiceTest extends TestCase
{
    /**
     * @var SecurityOrgServiceInterface
     */
    protected $service;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../DataSets/org.dataset.php');
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = TestHelper::getDbServiceManager()->get(SecurityOrgServiceInterface::class);
    }

    /**
     * @test
     * @ticket       CORE-993
     *
     * @param $user
     * @param $group
     * @param $expectedRole
     *
     * @dataProvider groupRoleProvider
     */
    public function testItShouldReturnCorrectRoleForUserInGroups($user, $group, $expectedRole)
    {
        $actualRole = $this->service->getRoleForGroup($group, $user);
        $this->assertSame(
            $expectedRole,
            $actualRole,
            'Incorrect role returned for user'
        );
    }

    /**
     * @return array
     */
    public function groupRoleProvider()
    {
        return [
            'Principal to English' => [
                'user'     => new Adult(['user_id' => 'principal']),
                'group'    => 'english',
                'expected' => 'principal.adult',
            ],

            'Principal to Math' => [
                'user'     => new Adult(['user_id' => 'principal']),
                'group'    => 'math',
                'expected' => 'principal.adult',
            ],

            'Principal to School' => [
                'user'     => new Adult(['user_id' => 'principal']),
                'group'    => 'math',
                'expected' => 'principal.adult',
            ],

            'Principal to Other Math' => [
                'user'     => new Adult(['user_id' => 'principal']),
                'group'    => 'other_math',
                'expected' => 'logged_in.adult',
            ],

            'Principal to Other School' => [
                'user'     => new Adult(['user_id' => 'principal']),
                'group'    => 'other_school',
                'expected' => 'logged_in.adult',
            ],
        ];
    }
}
