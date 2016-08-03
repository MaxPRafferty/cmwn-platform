<?php

namespace IntegrationTest\Service;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Service\SecurityOrgService;

/**
 * Test SecurityOrgServiceTest
 *
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
                'user'     => 'principal',
                'group'    => 'english',
                'expected' => 'principal',
            ],

            'Principal to Math' => [
                'user'     => 'principal',
                'group'    => 'math',
                'expected' => 'principal',
            ],

            'Principal to School' => [
                'user'     => 'principal',
                'group'    => 'math',
                'expected' => 'principal',
            ],

            'Principal to Other Math' => [
                'user'     => 'principal',
                'group'    => 'other_math',
                'expected' => null,
            ],

            'Principal to Other School' => [
                'user'     => 'principal',
                'group'    => 'other_school',
                'expected' => null,
            ],
        ];
    }
}
