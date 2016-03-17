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
                    ['permission' => 'view.all',     'label' => 'View All Entities'],
                    ['permission' => 'create.org',   'label' => 'Create an organization'],
                    ['permission' => 'edit.org',     'label' => 'Edit an organization'],
                    ['permission' => 'remove.org',   'label' => 'Delete an organization'],
                    ['permission' => 'remove.group', 'label' => 'Delete a group'],
                    ['permission' => 'create.user',  'label' => 'Create a User'],
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
                        'permission' => 'edit.child.group',
                        'label'      => 'Edit a group',
                        'entity'     => 'group',
                        'scope'      => 0,
                    ],
                    [
                        'permission' => 'create.child.group',
                        'label'      => 'Create a group',
                        'entity'     => 'group',
                        'scope'      => Rbac::SCOPE_CREATE,
                    ],
                    [
                        'permission' => 'remove.child.group',
                        'label'      => 'Edit a group',
                        'entity'     => 'group',
                        'scope'      => Rbac::SCOPE_REMOVE,
                    ],
                    ['permission' => 'import',             'label' => 'Import file'],
                ],
            ],

            'group_admin' => [
                'parents'     => ['admin'],
                'permissions' => [
                    ['permission' => 'edit.group',       'label' => 'Edit a group'],
                    ['permission' => 'child.code',       'label' => 'Send code to child'],
                    ['permission' => 'add.group.user',   'label' => 'Add user to group'],
                    ['permission' => 'remove.group.user','label' => 'Remove user to group'],
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

        $this->assertTrue(
            $rbac->isGranted('asstprincipal', 'edit.group')
        );

        $this->assertTrue(
            $rbac->isGranted('asstprincipal', 'edit.child.group')
        );

        $this->assertTrue(
            $rbac->isGranted('teacher', 'edit.group')
        );

        $this->assertFalse(
            $rbac->isGranted('teacher', 'edit.child.group')
        );
    }
}
