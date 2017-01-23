<?php

namespace GroupTest\Service;

use Group\Group;
use Group\Service\UserGroupService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Org\Organization;
use \PHPUnit_Framework_TestCase as TestCase;
use User\Adult;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Class UserGroupServiceTest
 *
 * @group Group
 * @group User
 * @group Service
 * @group UserGroupService
 */
class UserGroupServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

    /**
     * @test
     */
    public function testItShouldAttachUserToGroupWithRoleAsString()
    {
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actual) {
                $expected = [
                    'user_id'  => 'foobar',
                    'group_id' => 'bazbat',
                    'role'     => 'teacher',
                ];

                $this->assertEquals($expected, $actual);

                return 1;
            });

        $this->groupService->attachUserToGroup($this->group, $this->user, 'teacher');
    }

    /**
     * @test
     */
    public function testItShouldAttachUserToGroupWithRoleInterface()
    {
        $role = new GenericRole('teacher');
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->andReturnUsing(function ($actual) {
                $expected = [
                    'user_id'  => 'foobar',
                    'group_id' => 'bazbat',
                    'role'     => 'teacher',
                ];

                $this->assertEquals($expected, $actual);

                return 1;
            });

        $this->groupService->attachUserToGroup($this->group, $this->user, $role);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testItShouldFetchUsersForGroupUsingGroup()
    {
        $result = $this->groupService->fetchUsersForGroup($this->group);

        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Group Service did not return Paginator adapter'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchAllUsersForOrganizationUsingOrganizationId()
    {
        $result = $this->groupService->fetchUsersForOrg('fizzbuzz');

        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Group Service did not return Paginator adapter'
        );
    }

    /**
     * @test
     */
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
    }
}
