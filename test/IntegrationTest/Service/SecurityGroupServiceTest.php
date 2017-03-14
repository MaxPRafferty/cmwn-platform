<?php

namespace IntegrationTest\Service;

use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use PHPUnit\Framework\TestCase as TestCase;
use Security\Service\SecurityGroupServiceInterface;
use User\Adult;

/**
 * Test SecurityGroupServiceTest
 *
 * @group Db
 * @group Security
 * @group Organization
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SecurityGroupServiceTest extends TestCase
{
    /**
     * @var SecurityGroupServiceInterface
     */
    protected $service;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/org.dataset.php');
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = TestHelper::getServiceManager()->get(SecurityGroupServiceInterface::class);
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
