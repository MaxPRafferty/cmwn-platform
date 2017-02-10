<?php

namespace OrgTest\Service;

use Application\Exception\NotFoundException;
use Application\Utils\Type\TypeInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Org\Organization;
use Org\Service\OrganizationService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Predicate as Where;
use Zend\Db\TableGateway\TableGateway;

/**
 * Test OrganizationServiceTest
 *
 * @group Organization
 * @group Service
 * @group OrganizationService
 */
class OrganizationServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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
    public function setUpService()
    {
        $this->organizationService = new OrganizationService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateWay()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock(Adapter::class);
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock(TableGateway::class);
        $this->tableGateway->shouldReceive('getTable')->andReturn('orgs')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
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
    public function testItShouldSaveNewOrg()
    {
        $newOrg = new Organization();
        $newOrg->setTitle('school of rock');
        $newOrg->setType(TypeInterface::TYPE_GENERIC);

        $this->assertNull($newOrg->getCreated());
        $this->assertNull($newOrg->getUpdated());

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) use (&$newOrg) {
                $this->assertNotNull($newOrg->getCreated());
                $this->assertNotNull($newOrg->getUpdated());
                $this->assertNotEmpty($newOrg->getOrgId());

                $this->assertTrue(is_array($data));

                $expected         = $newOrg->getArrayCopy();
                $expected['meta'] = '[]';
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
            'type'        => TypeInterface::TYPE_GENERIC,
        ];

        $org    = new Organization($orgData);
        $result = new ResultSet();
        $result->initialize([$orgData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['org_id' => $orgData['org_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$org) {
                $this->assertEquals(['org_id' => $org->getOrgId()], $where);
                $expected         = $org->getArrayCopy();
                $expected['meta'] = '[]';

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
            'type'        => TypeInterface::TYPE_GENERIC,
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
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Organization not Found');

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
            'type'        => TypeInterface::TYPE_GENERIC,
        ];

        $org    = new Organization($orgData);
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
            'type'        => TypeInterface::TYPE_GENERIC,
        ];

        $org    = new Organization($orgData);
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
