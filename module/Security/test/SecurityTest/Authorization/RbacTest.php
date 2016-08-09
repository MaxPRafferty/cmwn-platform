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
            'Super Admin (group)'                => ['super', 'group', -1],
            'Super Admin (organization)'         => ['super', 'organization', -1],
            'Super Admin (adult)'                => ['super', 'adult', -1],
            'Super Admin (child)'                => ['super', 'child', -1],
            'Super Admin (me)'                   => ['super', 'me', -1],
            'Admin (group)'                      => ['admin.adult', 'group', 6],
            'Admin (organization)'               => ['admin.adult', 'organization', 0],
            'Admin (adult)'                      => ['admin.adult', 'adult', 1],
            'Admin (child)'                      => ['admin.adult', 'child', 3],
            'Admin (me)'                         => ['admin.adult', 'me', 2],
            'Group Admin (group)'                => ['group_admin.adult', 'group', 2],
            'Group Admin (organization)'         => ['group_admin.adult', 'organization', 0],
            'Group Admin (adult)'                => ['group_admin.adult', 'adult', 1],
            'Group Admin (child)'                => ['group_admin.adult', 'child', 3],
            'Group Admin (me)'                   => ['group_admin.adult', 'me', 2],
            'Principal (group)'                  => ['principal.adult', 'group', 3],
            'Principal (organization)'           => ['principal.adult', 'organization', 0],
            'Principal (adult)'                  => ['principal.adult', 'adult', 3],
            'Principal (child)'                  => ['principal.adult', 'child', 3],
            'Principal (me)'                     => ['principal.adult', 'me', 2],
            'Assistant Principal (group)'        => ['asst_principal.adult', 'group', 2],
            'Assistant Principal (organization)' => ['asst_principal.adult', 'organization', 0],
            'Assistant Principal (adult)'        => ['asst_principal.adult', 'adult', 1],
            'Assistant Principal (child)'        => ['asst_principal.adult', 'child', 3],
            'Assistant Principal (me)'           => ['asst_principal.adult', 'me', 2],
            'Teacher (group)'                    => ['teacher.adult', 'group', 2],
            'Teacher (organization)'             => ['teacher.adult', 'organization', 0],
            'Teacher (adult)'                    => ['teacher.adult', 'adult', 0],
            'Teacher (child)'                    => ['teacher.adult', 'child', 3],
            'Teacher (me)'                       => ['teacher.adult', 'me', 2],
            'Neighbor (group)'                   => ['neighbor.adult', 'group', 0],
            'Neighbor (organization)'            => ['neighbor.adult', 'organization', 0],
            'Neighbor (adult)'                   => ['neighbor.adult', 'adult', 0],
            'Neighbor (child)'                   => ['neighbor.adult', 'child', 0],
            'Neighbor (me)'                      => ['neighbor.adult', 'me', 0],
            'Logged In (group)'                  => ['logged_in.child', 'group', 0],
            'Logged In (organization)'           => ['logged_in.child', 'organization', 0],
            'Logged In (adult)'                  => ['logged_in.child', 'adult', 0],
            'Logged In (child)'                  => ['logged_in.child', 'child', 0],
            'Logged In (me)'                     => ['logged_in.child', 'me', 2],
            'Guest (group)'                      => ['guest', 'group', 0],
            'Guest (organization)'               => ['guest', 'organization', 0],
            'Guest (adult)'                      => ['guest', 'adult', 0],
            'Guest (child)'                      => ['guest', 'child', 0],
            'Guest (me)'                         => ['guest', 'me', 0],
        ];
    }

    /**
     * @return array
     */
    public function rolePermissionProvider()
    {
        return include __DIR__.'/_File/RbacDataProvider.php';
    }
}
