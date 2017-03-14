<?php

namespace GroupTest\Service;

use Group\Exception\RuntimeException;
use Group\Group;
use Group\Service\GroupService;
use Group\Service\UserGroupService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Org\Organization;
use PHPUnit\Framework\TestCase;
use User\Adult;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Rbac\RoleInterface;

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
    public function setUpService()
    {
        $this->groupService = new UserGroupService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateway()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\Adapter $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_groups')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
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

        $this->assertTrue(
            $this->groupService->attachUserToGroup($this->group, $this->user, 'teacher'),
            GroupService::class . ' did not return true when attaching group'
        );
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

        $this->assertTrue(
            $this->groupService->attachUserToGroup($this->group, $this->user, $role),
            GroupService::class . ' did not return true when attaching user with a ' . RoleInterface::class
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenRoleIsInvalidType()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Role must either be a sting or instance of ' . RoleInterface::class);

        $this->tableGateway->shouldReceive('insert')->never();
        $this->groupService->attachUserToGroup($this->group, $this->user, '');
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

        $this->assertTrue(
            $this->groupService->detachUserFromGroup($this->group, $this->user),
            GroupService::class . ' did not return true when detaching a user from a group'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchUsersForGroupUsingGroup()
    {
        $result = $this->groupService->fetchUsersForGroup($this->group);

        $this->assertInstanceOf(
            DbSelect::class,
            $result,
            GroupService::class . ' did not return Paginator adapter'
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
            DbSelect::class,
            $result,
            GroupService::class . ' did not return Paginator adapter'
        );
    }
}
