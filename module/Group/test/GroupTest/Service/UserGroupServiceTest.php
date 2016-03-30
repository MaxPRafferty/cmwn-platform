<?php

namespace GroupTest\Service;

use Group\Group;
use Group\Service\UserGroupService;
use Org\Organization;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Class UserGroupServiceTest
 * @package GroupTest\Service
 */
class UserGroupServiceTest extends TestCase
{
    /**
     * @var UserGroupService
     */
    protected $groupService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var Adult
     */
    protected $user;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_groups')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->groupService = new UserGroupService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Adult();
        $this->user->setUserId('foobar');
    }

    /**
     * @before
     */
    public function setUpGroup()
    {
        $this->group = new Group();
        $this->group->setGroupId('bazbat');
    }

    public function testItShouldAttachUserToGroupWithRoleAsString()
    {
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actual) {
                $expected = [
                    'user_id'  => 'foobar',
                    'group_id' => 'bazbat',
                    'role'     => 'teacher'
                ];

                $this->assertEquals($expected, $actual);
                return 1;
            });

        $this->groupService->attachUserToGroup($this->group, $this->user, 'teacher');
    }

    public function testItShouldAttachUserToGroupWithRoleInterface()
    {
        $role = new GenericRole('teacher');
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actual) {
                $expected = [
                    'user_id'  => 'foobar',
                    'group_id' => 'bazbat',
                    'role'     => 'teacher'
                ];

                $this->assertEquals($expected, $actual);
                return 1;
            });

        $this->groupService->attachUserToGroup($this->group, $this->user, $role);
    }

    public function testItShouldThrowExceptionWhenRoleIsInvalidType()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'Role must either be a sting or instance of Zend\PermissionAcl\RoleInterface'
        );

        $this->tableGateway->shouldReceive('insert')
            ->never();

        $this->groupService->attachUserToGroup($this->group, $this->user, []);
    }

    public function testItShouldDetachUserToGroupWithRoleAsString()
    {
        $this->tableGateway->shouldReceive('delete')
            ->once()
            ->andReturnUsing(function ($actual) {
                $expected = [
                    'user_id'  => 'foobar',
                    'group_id' => 'bazbat',
                ];

                $this->assertEquals($expected, $actual);
                return 1;
            });

        $this->groupService->detachUserFromGroup($this->group, $this->user);
    }

    public function testItShouldFetchUsersForGroupUsingGroupId()
    {
        $result = $this->groupService->fetchUsersForGroup('foo-bar');

        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Group Service did not return Paginator adapter'
        );

        $where = new Where();
        $where->addPredicate(new Operator('g.group_id', Operator::OP_EQ, 'foo-bar'));

        $select = new Select();
        $select->from(['u'  => 'users']);
        $select->join(['ug' => 'user_groups'], 'ug.user_id = u.user_id', [], Select::JOIN_LEFT);
        $select->join(['g'  => 'groups'], 'g.group_id = ug.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $this->assertEquals(
            new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                new HydratingResultSet(new ArraySerializable(), null)
            ),
            $result,
            'Incorrect result returned'
        );
    }

    public function testItShouldFetchUsersForGroupUsingGroup()
    {
        $result = $this->groupService->fetchUsersForGroup($this->group);

        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Group Service did not return Paginator adapter'
        );

        $where = new Where();
        $where->addPredicate(new Operator('g.group_id', Operator::OP_EQ, 'bazbat'));

        $select = new Select();
        $select->from(['u'  => 'users']);
        $select->join(['ug' => 'user_groups'], 'ug.user_id = u.user_id', [], Select::JOIN_LEFT);
        $select->join(['g'  => 'groups'], 'g.group_id = ug.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $this->assertEquals(
            new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                new HydratingResultSet(new ArraySerializable(), null)
            ),
            $result,
            'Incorrect result returned'
        );
    }

    public function testItShouldFetchAllUsersForOrganizationUsingOrganizationId()
    {
        $result = $this->groupService->fetchUsersForOrg('fizzbuzz');

        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Group Service did not return Paginator adapter'
        );

        $where = new Where();
        $where->addPredicate(new Operator('g.organization_id', Operator::OP_EQ, 'fizzbuzz'));

        $select = new Select();
        $select->from(['u'  => 'users']);
        $select->join(['ug' => 'user_groups'], 'ug.user_id = u.user_id', [], Select::JOIN_LEFT);
        $select->join(['g'  => 'groups'], 'g.group_id = ug.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $this->assertEquals(
            new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                new HydratingResultSet(new ArraySerializable(), null)
            ),
            $result,
            'Incorrect result returned'
        );
    }
    public function testItShouldFetchAllUsersForOrganizationUsingOrganization()
    {
        $org = new Organization();
        $org->setOrgId('fizzbuzz');
        $result = $this->groupService->fetchUsersForOrg($org);

        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Group Service did not return Paginator adapter'
        );

        $where = new Where();
        $where->addPredicate(new Operator('g.organization_id', Operator::OP_EQ, 'fizzbuzz'));

        $select = new Select();
        $select->from(['u'  => 'users']);
        $select->join(['ug' => 'user_groups'], 'ug.user_id = u.user_id', [], Select::JOIN_LEFT);
        $select->join(['g'  => 'groups'], 'g.group_id = ug.group_id', [], Select::JOIN_LEFT);
        $select->where($where);

        $this->assertEquals(
            new DbSelect(
                $select,
                $this->tableGateway->getAdapter(),
                new HydratingResultSet(new ArraySerializable(), null)
            ),
            $result,
            'Incorrect result returned'
        );
    }
}
