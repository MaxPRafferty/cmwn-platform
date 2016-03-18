<?php

namespace SecurityTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\Rbac;

/**
 * Exception RbacTest
 *
 * ${CARET}
 */
class RbacTest extends TestCase
{
    public function testItShouldDoTest()
    {
        $roles = [
            'super' => [
                'permissions' => [
                    [
                        'permission' => 'view.all',
                        'label'      => 'View All Entities'
                    ],
                    [
                        'permission' => 'create.org',
                        'label'      => 'Create an organization',
                        'entity'     => 'organization',
                        'scope'      => Rbac::SCOPE_CREATE
                    ],
                    [
                        'permission' => 'edit.org',
                        'label'      => 'Edit an organization',
                        'entity'     => 'organization',
                        'scope'      => Rbac::SCOPE_UPDATE
                    ],
                    [
                        'permission' => 'remove.org',
                        'label'      => 'Delete an organization',
                        'entity'     => 'organization',
                        'scope'      => Rbac::SCOPE_REMOVE
                    ],
                    [
                        'permission' => 'remove.group',
                        'label'      => 'Delete a group',
                        'entity'     => 'group',
                        'scope'      => Rbac::SCOPE_REMOVE
                    ],
                    [
                        'permission' => 'create.user',
                        'label'      => 'Create a User'
                    ],
                    ['permission' => 'edit.user',    'label' => 'Edit a User'],
                    ['permission' => 'remove.user',  'label' => 'Delete a User'],
                ]
            ],

            'admin' => [
                'parents'     => ['super'],
                'permissions' => [
                    [
                        'permission' => 'adult.code',
                        'label'      => 'Send code to adult',
                        'entity'     => 'adult'
                    ],
                    [
                        'permission' => 'create.group',
                        'label'      => 'Create a group',
                        'entity'     => 'group',
                        'scope'      => Rbac::SCOPE_CREATE,
                    ],
                    [
                        'permission' => 'remove.child.group',
                        'label'      => 'Remove a sub group',
                    ],
                    [
                        'permission' => 'create.child.group',
                        'label'      => 'Create a sub group',
                    ],
                    [
                        'permission' => 'import',
                        'label'      => 'Import file'
                    ],
                ],
            ],

            'group_admin' => [
                'parents'     => ['admin'],
                'permissions' => [
                    [
                        'permission' => 'edit.group',
                        'label'      => 'Edit a group',
                        'entity'     => 'group',
                        'scope'      => Rbac::SCOPE_UPDATE,
                    ],
                    [
                        'permission' => 'child.code',
                        'label'      => 'Send code to child'
                    ],
                    [
                        'permission' => 'add.group.user',
                        'label'      => 'Add user to group'
                    ],
                    [
                        'permission' => 'remove.group.user',
                        'label'      => 'Remove user to group'
                    ],
                ]
            ],

            'principal' => [
                'siblings' => ['group_admin', 'admin']
            ],
            
            'asstprincipal' => [
                'siblings' => ['group_admin', 'admin']
            ],

            'teacher' => [
                'parents'     => ['admin'],
                'siblings'    => 'group_admin',
            ],

            'guest' => [
                'parents'     => ['group_admin'],
            ]
        ];

        $rbac = new Rbac($roles);

        $this->assertEquals(
            7,
            $rbac->getScopeForEntity('super', 'organization'),
            'Super has incorrect bits for Organization'
        );

        $this->assertEquals(
            7,
            $rbac->getScopeForEntity('super', 'group'),
            'Super has incorrect bits for Group'
        );

        $this->assertEquals(
            0,
            $rbac->getScopeForEntity('admin', 'organization'),
            'Admin has incorrect bits for Organization'
        );

        $this->assertEquals(
            7,
            $rbac->getScopeForEntity('admin', 'group'),
            'Admin has incorrect bits for Group'
        );

        $this->assertEquals(
            0,
            $rbac->getScopeForEntity('group_admin', 'organization'),
            'Group Admin has incorrect bits for Organization'
        );

        $this->assertEquals(
            Rbac::SCOPE_UPDATE,
            $rbac->getScopeForEntity('group_admin', 'group'),
            'Group Admin has incorrect bits for Group'
        );

        $this->assertEquals(0, $rbac->getScopeForEntity('teacher', 'organization'));
        $this->assertEquals(Rbac::SCOPE_UPDATE, $rbac->getScopeForEntity('teacher', 'group'));
    }
}
