<?php

namespace SecurityTest\Authorization;

use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authorization\Rbac;

/**
 * Exception RbacTest
 *
 * ${CARET}
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
        $this->rbac = TestHelper::getServiceManager()->get('Security\Authorization\Rbac');
    }

    /**
     * @before
     */
    public function setUpPermissionsFromConfig()
    {
        $config      = TestHelper::getServiceManager()->get('config');
        $rolesConfig = $config['cmwn-roles'];

        foreach ($rolesConfig as $role => $roleConfig) {
            foreach ($roleConfig['permissions'] as $permission) {
                array_push($this->allPermissions, $permission['permission']);
            }
        }

        sort($this->allPermissions);
    }

    /**
     * @param $role
     * @param array $allowed
     * @param array $denied
     * @dataProvider rolePermissionProvider
     */
    public function testRolesShouldHaveCorrectPermission($role, array $allowed, array $denied)
    {
        $actual = array_merge($allowed, $denied);
        sort($actual);
        $this->assertEquals(
            $this->allPermissions,
            $actual,
            'Missing Permissions from provider'
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
            'Admin (group)'                      => ['admin', 'group', 6],
            'Admin (organization)'               => ['admin', 'organization', 0],
            'Group Admin (group)'                => ['group_admin', 'group', 2],
            'Group Admin (organization)'         => ['group_admin', 'organization', 0],
            'Principal (group)'                  => ['principal', 'group', 6],
            'Principal (organization)'           => ['principal', 'organization', 0],
            'Assistant Principal (group)'        => ['asst_principal', 'group', 6],
            'Assistant Principal (organization)' => ['asst_principal', 'organization', 0],
            'Teacher (group)'                    => ['teacher', 'group', 2],
            'Teacher (organization)'             => ['teacher', 'organization', 0],
            'Logged In (group)'                  => ['logged_in', 'group', 0],
            'Logged In (organization)'           => ['logged_in', 'organization', 0],
            'Guest (group)'                      => ['guest', 'group', 0],
            'Guest (organization)'               => ['guest', 'organization', 0],
        ];
    }

    /**
     * @return array
     */
    public function rolePermissionProvider()
    {
        return [
            'Super Admin' => [
                'role'    => 'super',
                'allowed' => [
                    'view.all',
                    'create.org',
                    'edit.org',
                    'view.all.orgs',
                    'remove.org',
                    'remove.group',
                    'create.user',
                    'edit.user',
                    'remove.user',
                    'adult.code',
                    'create.group',
                    'remove.child.group',
                    'create.child.group',
                    'import',
                    'edit.group',
                    'child.code',
                    'add.group.user',
                    'remove.group.user',
                    'view.group.users',
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],

                'denied' => [

                ],
            ],

            'Admin' => [
                'role'    => 'admin',
                'allowed' => [
                    'adult.code',
                    'create.group',
                    'remove.child.group',
                    'create.child.group',
                    'import',
                    'edit.group',
                    'child.code',
                    'add.group.user',
                    'remove.group.user',
                    'view.group.users',
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],

                'denied' => [
                    'edit.user',
                    'remove.user',
                    'view.all',
                    'create.org',
                    'edit.org',
                    'view.all.orgs',
                    'remove.org',
                    'remove.group',
                    'create.user',
                ],
            ],

            'Group Admin' => [
                'role'    => 'group_admin',
                'allowed' => [
                    'edit.group',
                    'child.code',
                    'add.group.user',
                    'remove.group.user',
                    'view.group.users',
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],

                'denied' => [
                    'adult.code',
                    'create.group',
                    'remove.child.group',
                    'create.child.group',
                    'import',
                    'edit.user',
                    'remove.user',
                    'view.all',
                    'create.org',
                    'edit.org',
                    'view.all.orgs',
                    'remove.org',
                    'remove.group',
                    'create.user',
                ],
            ],

            'Principal' => [
                'role'    => 'principal',
                'allowed' => [
                    'adult.code',
                    'create.group',
                    'remove.child.group',
                    'create.child.group',
                    'import',
                    'edit.group',
                    'child.code',
                    'add.group.user',
                    'remove.group.user',
                    'view.group.users',
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],

                'denied' => [
                    'edit.user',
                    'remove.user',
                    'view.all',
                    'create.org',
                    'edit.org',
                    'view.all.orgs',
                    'remove.org',
                    'remove.group',
                    'create.user',
                ],
            ],

            'Assistant Principal' => [
                'role'    => 'asst_principal',
                'allowed' => [
                    'adult.code',
                    'create.group',
                    'remove.child.group',
                    'create.child.group',
                    'import',
                    'edit.group',
                    'child.code',
                    'add.group.user',
                    'remove.group.user',
                    'view.group.users',
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],

                'denied' => [
                    'edit.user',
                    'remove.user',
                    'view.all',
                    'create.org',
                    'edit.org',
                    'view.all.orgs',
                    'remove.org',
                    'remove.group',
                    'create.user',
                ],
            ],
        ];
    }
}
