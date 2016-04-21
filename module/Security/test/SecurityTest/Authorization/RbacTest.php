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
            'Super Admin (me)'                   => ['super', 'me', -1],
            'Admin (group)'                      => ['admin', 'group', 6],
            'Admin (organization)'               => ['admin', 'organization', 0],
            'Admin (user)'                       => ['admin', 'user', 0],
            'Admin (me)'                         => ['admin', 'me', 3],
            'Group Admin (group)'                => ['group_admin', 'group', 2],
            'Group Admin (organization)'         => ['group_admin', 'organization', 0],
            'Group Admin (user)'                 => ['group_admin', 'user', 0],
            'Group Admin (me)'                   => ['group_admin', 'me', 3],
            'Principal (group)'                  => ['principal', 'group', 3],
            'Principal (organization)'           => ['principal', 'organization', 0],
            'Principal (user)'                   => ['principal', 'user', 3],
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
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user.adult',
                    'remove.user.child',
                    'update.password',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                    'view.all.users',
                ],

                'denied' => [
                    'pick.username',
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
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user.adult',
                    'pick.username',
                    'remove.group',
                    'remove.org',
                    'remove.user.adult',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.all.users',
                ],
            ],

            'Group Admin' => [
                'role'    => 'group_admin',
                'allowed' => [
                    'add.group.user',
                    'child.code',
                    'edit.group',
                    'edit.user.child',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user.adult',
                    'import',
                    'pick.username',
                    'remove.child.group',
                    'remove.group',
                    'remove.org',
                    'remove.user.adult',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.all.users',
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
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user.adult',
                    'pick.username',
                    'remove.group',
                    'remove.org',
                    'remove.user.adult',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.all.users',
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
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user.adult',
                    'pick.username',
                    'remove.group',
                    'remove.org',
                    'remove.user.adult',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.all.users',
                ],
            ],
            'Teacher' => [
                'role'    => 'teacher',
                'allowed' => [
                    'add.group.user',
                    'child.code',
                    'edit.group',
                    'edit.user.child',
                    'remove.group.user',
                    'remove.user.child',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.org',
                    'edit.user.adult',
                    'import',
                    'pick.username',
                    'remove.child.group',
                    'remove.group',
                    'remove.org',
                    'remove.user.adult',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.org.users',
                    'view.all.users',
                ],
            ],

            'Logged In' => [
                'role'    => 'logged_in',
                'allowed' => [
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.group',
                    'edit.org',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'pick.username',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.group.users',
                    'view.org.users',
                    'view.all.users',
                ],
            ],

            'Child' => [
                'role'    => 'child',
                'allowed' => [
                    'child.code',
                    'pick.username',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'add.group.user',
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.group',
                    'edit.org',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.org.users',
                    'view.all.users',
                ],
            ],

            'Student' => [
                'role'    => 'student',
                'allowed' => [
                    'child.code',
                    'pick.username',
                    'update.password',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.user.adult',
                    'view.user.child',
                ],

                'denied' => [
                    'add.group.user',
                    'adult.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.group',
                    'edit.org',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user.adult',
                    'remove.user.child',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.org.users',
                    'view.all.users',
                ],
            ],

            'Guest' => [
                'role'    => 'guest',
                'allowed' => [

                ],

                'denied' => [
                    'add.group.user',
                    'adult.code',
                    'child.code',
                    'create.child.group',
                    'create.group',
                    'create.org',
                    'create.user',
                    'edit.group',
                    'edit.org',
                    'edit.user.adult',
                    'edit.user.child',
                    'import',
                    'pick.username',
                    'remove.child.group',
                    'remove.group',
                    'remove.group.user',
                    'remove.org',
                    'remove.user.adult',
                    'remove.user.child',
                    'update.password',
                    'view.all.groups',
                    'view.all.orgs',
                    'view.games',
                    'view.group',
                    'view.group.users',
                    'view.org',
                    'view.org.users',
                    'view.user.adult',
                    'view.user.child',
                    'view.all.users',
                ],
            ],
        ];
    }
}
