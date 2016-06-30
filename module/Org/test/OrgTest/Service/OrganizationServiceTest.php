<?php

namespace OrgTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Org\Organization;
use Org\Service\OrganizationService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Predicate as Where;

/**
 * Test OrganizationServiceTest
 *
 * @group Organization
 * @group Service
 * @group OrganizationService
 */
class OrganizationServiceTest extends TestCase
{
    /**
     * @var OrganizationService
     */
    protected $organizationService;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('orgs')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->organizationService = new OrganizationService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->organizationService->fetchAll(null);
        $this->assertInstanceOf('\Zend\Paginator\Adapter\AdapterInterface', $result);
    }

    /**
     * @test
     */
    public function testItShouldReturnIteratorOnFetchAllWithNoWhereAndNotPaginating()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($where) {
                $this->assertInstanceOf('Zend\Db\Sql\Predicate\Predicate', $where);
                return new \ArrayIterator([]);
            })
            ->once();

        $result = $this->organizationService->fetchAll(null, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    /**
     * @test
     */
    public function testItShouldReturnIteratorPassWhereWhenGivenWhereAndNotPaginating()
    {
        $expectedWhere = new Where();
        $this->tableGateway
            ->shouldReceive('select')
            ->andReturnUsing(function ($where) use (&$expectedWhere) {
                /** @var \Zend\Db\Sql\Predicate\Predicate $where */
                $this->assertSame($expectedWhere, $where);
                return new \ArrayIterator([]);

            })
            ->once();

        $result = $this->organizationService->fetchAll($expectedWhere, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    /**
     * @test
     */
    public function testItShouldSaveNewOrg()
    {
        $newOrg = new Organization();

        $this->assertNull($newOrg->getCreated());
        $this->assertNull($newOrg->getUpdated());
        $this->assertEmpty($newOrg->getOrgId());

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) use (&$newOrg) {
                $this->assertNotNull($newOrg->getCreated());
                $this->assertNotNull($newOrg->getUpdated());
                $this->assertNotEmpty($newOrg->getOrgId());

                $this->assertTrue(is_array($data));

                $expected = $newOrg->getArrayCopy();
                $expected['meta'] = '[]';
                unset($expected['password']);
                unset($expected['deleted']);
                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals($expected, $data);
                return 1;
            })
            ->once();

        $this->assertTrue($this->organizationService->createOrganization($newOrg));
    }

    /**
     * @test
     */
    public function testItShouldUpdateExistingOrg()
    {
        $orgData = [
            'org_id'      => 'abcd-efgh-ijklm-nop',
            'title'       => 'manchuck',
            'description' => 'chuck@manchuck.com',
            'meta'        => [],
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '2016-02-28',
        ];

        $org   = new Organization($orgData);
        $result = new ResultSet();
        $result->initialize([$orgData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['org_id' => $orgData['org_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$org) {
                $this->assertEquals(['org_id' => $org->getOrgId()], $where);
                $expected = $org->getArrayCopy();
                $expected['meta'] = '[]';

                unset($expected['password']);
                unset($expected['deleted']);
                unset($expected['created']);
                unset($expected['org_id']);
                $this->assertArrayNotHasKey('deleted', $data);

                $this->assertEquals($expected, $data);

            });

        $this->assertTrue($this->organizationService->updateOrganization($org));
    }

    /**
     * @test
     */
    public function testItShouldFetchOrgById()
    {
        $orgData = [
            'org_id'      => 'abcd-efgh-ijklm-nop',
            'title'       => 'manchuck',
            'description' => 'chuck@manchuck.com',
            'meta'        => [],
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '2016-02-28',
        ];

        $result = new ResultSet();
        $result->initialize([$orgData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['org_id' => $orgData['org_id']])
            ->andReturn($result);

        $this->assertInstanceOf('Org\Organization', $this->organizationService->fetchOrganization($orgData['org_id']));
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenOrgIsNotFound()
    {
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'Organization not Found'
        );

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->organizationService->fetchOrganization('foo');
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteByDefault()
    {
        $orgData = [
            'org_id'      => 'abcd-efgh-ijklm-nop',
            'title'       => 'manchuck',
            'description' => 'chuck@manchuck.com',
            'meta'        => [],
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '2016-02-28',
        ];

        $org   = new Organization($orgData);
        $result = new ResultSet();
        $result->initialize([$orgData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['org_id' => $orgData['org_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$org) {
                $this->assertEquals(['org_id' => $org->getOrgId()], $where);
                $this->assertNotEmpty($data['deleted']);

            });

        $this->assertTrue($this->organizationService->deleteOrganization($org));
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteWhenForced()
    {
        $orgData = [
            'org_id'      => 'abcd-efgh-ijklm-nop',
            'title'       => 'manchuck',
            'description' => 'chuck@manchuck.com',
            'meta'        => [],
            'created'     => '2016-02-28',
            'updated'     => '2016-02-28',
            'deleted'     => '2016-02-28',
        ];

        $org   = new Organization($orgData);
        $result = new ResultSet();
        $result->initialize([$orgData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['org_id' => $orgData['org_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('delete')
            ->andReturnUsing(function ($where) use (&$org) {
                $this->assertEquals(['org_id' => $org->getOrgId()], $where);

            });

        $this->assertTrue($this->organizationService->deleteOrganization($org, false));
    }
}
