<?php

namespace IntegrationTest\Service;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Service\SecurityOrgService;
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
     * @var SecurityOrgService
     */
    protected $service;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = TestHelper::getServiceManager()->get(SecurityOrgService::class);
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
