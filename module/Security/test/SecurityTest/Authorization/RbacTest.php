<?php

namespace SecurityTest\Authorization;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authorization\Rbac;

/**
 * Test RbacTest
 *
 * @group Security
 * @group Authorization
 * @group Rbac
 * @group User
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RbacTest extends TestCase
{
    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @var array
     */
    protected $allPermissions = [];

    /**
     * @before
     */
    public function setUpRbac()
    {
        // pull rbac from the config since we are test the real config not the fake one
        $this->rbac = TestHelper::getServiceManager()->get(Rbac::class);
    }

    /**
     * @before
     */
    public function setUpPermissionsFromConfig()
    {
        $config      = TestHelper::getServiceManager()->get('config');
        $rolesConfig = $config['cmwn-roles'];

        $this->allPermissions = array_keys($rolesConfig['permission_labels']);
        $this->allPermissions = array_unique($this->allPermissions);
        sort($this->allPermissions);
    }

    /**
     * @param $role
     * @param array $allowed
     * @param array $denied
     *
     * @dataProvider rolePermissionProvider
     */
    public function testRolesShouldHaveCorrectPermission($role, array $allowed, array $denied)
    {
        $actual = array_merge($allowed, $denied);
        sort($actual);
        $this->assertEquals(
            $this->allPermissions,
            $actual,
            'Missing Permissions from provider for role: ' . $role
        );

        foreach ($allowed as $permission) {
            $this->assertTrue(
                $this->rbac->isGranted($role, $permission),
                sprintf('Permission %s is not allowed for role %s', $permission, $role)
            );
        }

        foreach ($denied as $permission) {
            $this->assertFalse(
                $this->rbac->isGranted($role, $permission),
                sprintf('Permission %s is allowed for role %s', $permission, $role)
            );
        }
    }

    /**
     * @param $role
     * @param $entity
     * @param $expected
     *
     * @dataProvider roleEntityProvider
     */
    public function testRolesShouldHaveCorrectPermissionsBits($role, $entity, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->rbac->getScopeForEntity($role, $entity),
            sprintf('%s user should have %s scope for %s', $role, $entity, $expected)
        );
    }

    /**
     * @return array
     */
    public function roleEntityProvider()
    {
        return [
            'Super Admin (district group)'                => ['super', 'group.district', -1],
            'Super Admin (school group)'                  => ['super', 'group.school', -1],
            'Super Admin (class group)'                   => ['super', 'group.class', -1],
            'Super Admin (district organization)'         => ['super', 'organization.district', -1],
            'Super Admin (school organization)'           => ['super', 'organization.school', -1],
            'Super Admin (class organization)'            => ['super', 'organization.class', -1],
            'Super Admin (adult)'                         => ['super', 'adult', -1],
            'Super Admin (child)'                         => ['super', 'child', -1],
            'Super Admin (me)'                            => ['super', 'me', -1],
            'Admin (district group)'                      => ['admin.adult', 'group.district', 0],
            'Admin (school group)'                        => ['admin.adult', 'group.school', 6],
            'Admin (class group)'                         => ['admin.adult', 'group.class', 6],
            'Admin (district organization)'               => ['admin.adult', 'organization.district', 0],
            'Admin (school organization)'                 => ['admin.adult', 'organization.school', 0],
            'Admin (class organization)'                  => ['admin.adult', 'organization.class', 0],
            'Admin (adult)'                               => ['admin.adult', 'adult', 1],
            'Admin (child)'                               => ['admin.adult', 'child', 3],
            'Admin (me)'                                  => ['admin.adult', 'me', 2],
            'Group Admin (district group)'                => ['group_admin.adult', 'group.district', 0],
            'Group Admin (school group)'                  => ['group_admin.adult', 'group.school', 2],
            'Group Admin (class group)'                   => ['group_admin.adult', 'group.class', 2],
            'Group Admin (district organization)'         => ['group_admin.adult', 'organization.district', 0],
            'Group Admin (school organization)'           => ['group_admin.adult', 'organization.school', 0],
            'Group Admin (class organization)'            => ['group_admin.adult', 'organization.class', 0],
            'Group Admin (adult)'                         => ['group_admin.adult', 'adult', 1],
            'Group Admin (child)'                         => ['group_admin.adult', 'child', 3],
            'Group Admin (me)'                            => ['group_admin.adult', 'me', 2],
            'Principal (district group)'                  => ['principal.adult', 'group.district', 0],
            'Principal (school group)'                    => ['principal.adult', 'group.school', 2],
            'Principal (class group)'                     => ['principal.adult', 'group.class', 3],
            'Principal (district organization)'           => ['principal.adult', 'organization.district', 0],
            'Principal (school organization)'             => ['principal.adult', 'organization.school', 0],
            'Principal (class organization)'              => ['principal.adult', 'organization.class', 0],
            'Principal (adult)'                           => ['principal.adult', 'adult', 3],
            'Principal (child)'                           => ['principal.adult', 'child', 3],
            'Principal (me)'                              => ['principal.adult', 'me', 2],
            'Assistant Principal (district group)'        => ['asst_principal.adult', 'group.district', 0],
            'Assistant Principal (school group)'          => ['asst_principal.adult', 'group.school', 2],
            'Assistant Principal (class group)'           => ['asst_principal.adult', 'group.class', 2],
            'Assistant Principal (district organization)' => ['asst_principal.adult', 'organization.district', 0],
            'Assistant Principal (school organization)'   => ['asst_principal.adult', 'organization.school', 0],
            'Assistant Principal (class organization)'    => ['asst_principal.adult', 'organization.class', 0],
            'Assistant Principal (adult)'                 => ['asst_principal.adult', 'adult', 1],
            'Assistant Principal (child)'                 => ['asst_principal.adult', 'child', 3],
            'Assistant Principal (me)'                    => ['asst_principal.adult', 'me', 2],
            'Teacher (district group)'                    => ['teacher.adult', 'group.district', 0],
            'Teacher (school group)'                      => ['teacher.adult', 'group.school', 0],
            'Teacher (class group)'                       => ['teacher.adult', 'group.class', 2],
            'Teacher (district organization)'             => ['teacher.adult', 'organization.district', 0],
            'Teacher (school organization)'               => ['teacher.adult', 'organization.school', 0],
            'Teacher (class organization)'                => ['teacher.adult', 'organization.class', 0],
            'Teacher (adult)'                             => ['teacher.adult', 'adult', 0],
            'Teacher (child)'                             => ['teacher.adult', 'child', 3],
            'Teacher (me)'                                => ['teacher.adult', 'me', 2],
            'Guest (district group)'                      => ['guest', 'group.district', 0],
            'Guest (school group)'                        => ['guest', 'group.school', 0],
            'Guest (class group)'                         => ['guest', 'group.class', 0],
            'Guest (district organization)'               => ['guest', 'organization.district', 0],
            'Guest (school organization)'                 => ['guest', 'organization.school', 0],
            'Guest (class organization)'                  => ['guest', 'organization.class', 0],
            'Guest (adult)'                               => ['guest', 'adult', 0],
            'Guest (child)'                               => ['guest', 'child', 0],
            'Guest (me)'                                  => ['guest', 'me', 0],
        ];
    }

    /**
     * @return array
     */
    public function rolePermissionProvider()
    {
        return include __DIR__ . '/_File/RbacDataProvider.php';
    }
}
