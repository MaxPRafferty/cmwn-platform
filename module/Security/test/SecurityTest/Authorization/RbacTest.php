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
        // pull rbac from the config since we are test the real config not the fake one
        $this->rbac = TestHelper::getServiceManager()->get('Security\Authorization\Rbac');
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
            'Super Admin (user)'                 => ['super', 'user', -1],
            'Super Admin (me)'                   => ['super', 'me', 3],
            'Admin (group)'                      => ['admin', 'group', 6],
            'Admin (organization)'               => ['admin', 'organization', 0],
            'Admin (user)'                       => ['admin', 'user', 0],
            'Admin (me)'                         => ['admin', 'me', 3],
            'Group Admin (group)'                => ['group_admin', 'group', 2],
            'Group Admin (organization)'         => ['group_admin', 'organization', 0],
            'Group Admin (user)'                 => ['group_admin', 'user', 0],
            'Group Admin (me)'                   => ['group_admin', 'me', 3],
            'Principal (group)'                  => ['principal', 'group', 2],
            'Principal (organization)'           => ['principal', 'organization', 0],
            'Principal (user)'                   => ['principal', 'user', 2],
            'Principal (me)'                     => ['principal', 'me', 3],
            'Assistant Principal (group)'        => ['asst_principal', 'group', 2],
            'Assistant Principal (organization)' => ['asst_principal', 'organization', 0],
            'Assistant Principal (user)'         => ['asst_principal', 'user', 2],
            'Assistant Principal (me)'           => ['asst_principal', 'me', 3],
            'Teacher (group)'                    => ['teacher', 'group', 2],
            'Teacher (organization)'             => ['teacher', 'organization', 0],
            'Teacher (user)'                     => ['teacher', 'user', 2],
            'Teacher (me)'                       => ['teacher', 'me', 3],
            'Logged In (group)'                  => ['logged_in', 'group', 0],
            'Logged In (organization)'           => ['logged_in', 'organization', 0],
            'Logged In (user)'                   => ['logged_in', 'user', 0],
            'Logged In (me)'                     => ['logged_in', 'me', 3],
            'Guest (group)'                      => ['guest', 'group', 0],
            'Guest (organization)'               => ['guest', 'organization', 0],
            'Guest (user)'                       => ['guest', 'user', 0],
            'Guest (me)'                         => ['guest', 'me', 0],
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
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.group',
                    'edit.org',
                    'edit.user',
                    'import',
                    'read.group',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user',
                    'update.password',
                    'view.all.orgs',
                    'view.games',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                ],

                'denied' => [
                ],
            ],

            'Admin' => [
                'role'    => 'admin',
                'allowed' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'import',
                    'read.group',
                    'remove.child.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                ],

                'denied' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                ],
            ],

            'Group Admin' => [
                'role'    => 'group_admin',
                'allowed' => [
                    'add.group.user',
                    'child.code',
                    'edit.group',
                    'read.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                ],

                'denied' => [
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user',
                    'import',
                    'remove.child.group',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                ],
            ],

            'Principal' => [
                'role'    => 'principal',
                'allowed' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'import',
                    'read.group',
                    'remove.child.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                ],

                'denied' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                    'view.org.users',
                ],
            ],

            'Assistant Principal' => [
                'role'    => 'asst_principal',
                'allowed' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'edit.group',
                    'import',
                    'read.group',
                    'remove.child.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                ],

                'denied' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                    'view.org.users',
                ],
            ],
            'Teacher' => [
                'role'    => 'teacher',
                'allowed' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'edit.group',
                    'read.group',
                    'remove.group.user',
                    'update.password',
                    'view.games',
                    'view.group.users',
                    'view.org',
                ],

                'denied' => [
                    'create.org',
                    'create.child.group',
                    'create.group',
                    'create.user',
                    'import',
                    'edit.org',
                    'edit.user',
                    'remove.child.group',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                    'view.org.users',
                ],
            ],
            'Logged In' => [
                'role'    => 'logged_in',
                'allowed' => [
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                ],

                'denied' => [
                    'create.org',
                    'create.child.group',
                    'create.group',
                    'create.user',
                    'import',
                    'edit.org',
                    'edit.user',
                    'remove.child.group',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                    'view.org.users',
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'edit.group',
                    'remove.group.user',
                    'view.group.users',
                ],
            ],

            'Guest' => [
                'role'    => 'guest',
                'allowed' => [

                ],

                'denied' => [
                    'read.group',
                    'update.password',
                    'view.games',
                    'view.org',
                    'create.org',
                    'create.child.group',
                    'create.group',
                    'create.user',
                    'import',
                    'edit.org',
                    'edit.user',
                    'remove.child.group',
                    'remove.group',
                    'remove.org',
                    'remove.user',
                    'view.all.orgs',
                    'view.org.users',
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'edit.group',
                    'remove.group.user',
                    'view.group.users',
                ],
            ],
        ];
    }
}
