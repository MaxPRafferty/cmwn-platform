<?php

namespace OrgTest\Delegator;

use \PHPUnit_Framework_TestCase as TestCase;
use Org\Organization;
use Org\Delegator\OrganizationServiceDelegator;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;


/**
 * Test OrganizationServiceDelegatorTest
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
class OrganizationServiceDelegatorTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Org\Service\OrganizationService
     */
    protected $orgService;

    /**
     * @var OrganizationServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var Organization
     */
    protected $org;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->orgService = \Mockery::mock('\Org\Service\OrganizationService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator    = new OrganizationServiceDelegator($this->orgService);
        $this->delegator->getEventManager()->clearListeners('save.org');
        $this->delegator->getEventManager()->clearListeners('fetch.org.post');
        $this->delegator->getEventManager()->clearListeners('fetch.all.orgs');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpOrg()
    {
        $this->org = new Organization();
        $this->org->setOrgId(md5('foobar'));
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams()
        ];
    }

    public function testItShouldCallSaveOrg()
    {
        $this->orgService->shouldReceive('saveOrg')
            ->with($this->org)
            ->andReturn(true)
            ->once();


        $this->delegator->saveOrg($this->org);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.org',
                'target' => $this->orgService,
                'params' => ['org' => $this->org]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.org.post',
                'target' => $this->orgService,
                'params' => ['org' => $this->org]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallSaveOrgWhenEventPrevents()
    {
        $this->orgService->shouldReceive('saveOrg')
            ->with($this->org)
            ->never();

        $this->delegator->getEventManager()->attach('save.org', function (Event $event) {
            $event->stopPropagation(true);
            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->saveOrg($this->org));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.org',
                'target' => $this->orgService,
                'params' => ['org' => $this->org]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallFetchOrg()
    {
        $this->orgService->shouldReceive('fetchOrg')
            ->with($this->org->getOrgId())
            ->andReturn($this->org)
            ->once();

        $this->assertSame(
            $this->org,
            $this->delegator->fetchOrg($this->org->getOrgId())
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org',
                'target' => $this->orgService,
                'params' => ['org_id' => $this->org->getOrgId()]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.org.post',
                'target' => $this->orgService,
                'params' => ['org' => $this->org, 'org_id' => $this->org->getOrgId()]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallFetchOrgAndReturnEventResult()
    {
        $this->orgService->shouldReceive('fetchOrg')
            ->with($this->org->getOrgId())
            ->andReturn($this->org)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.org', function (Event $event) {
            $event->stopPropagation(true);
            return $this->org;
        });

        $this->assertSame(
            $this->org,
            $this->delegator->fetchOrg($this->org->getOrgId())
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.org',
                'target' => $this->orgService,
                'params' => ['org_id' => $this->org->getOrgId()]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallDeleteOrg()
    {
        $this->orgService->shouldReceive('deleteOrg')
            ->with($this->org, true)
            ->andReturn($this->org)
            ->once();

        $this->assertSame(
            $this->org,
            $this->delegator->deleteOrg($this->org)
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.org',
                'target' => $this->orgService,
                'params' => ['org' => $this->org, 'soft' => true]
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.org.post',
                'target' => $this->orgService,
                'params' => ['org' => $this->org, 'soft' => true]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldNotCallDeleteOrgAndReturnEventResult()
    {
        $this->orgService->shouldReceive('deleteOrg')
            ->with($this->org, true)
            ->andReturn($this->org)
            ->never();

        $this->delegator->getEventManager()->attach('delete.org', function (Event $event) {
            $event->stopPropagation(true);
            return $this->org;
        });

        $this->assertSame(
            $this->org,
            $this->delegator->deleteOrg($this->org)
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.org',
                'target' => $this->orgService,
                'params' => ['org' => $this->org, 'soft' => true]
            ],
            $this->calledEvents[0]
        );
    }

    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->orgService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->once();

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.orgs',
                'target' => $this->orgService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null]
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.orgs.post',
                'target' => $this->orgService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null, 'orgs' => $result]
            ],
            $this->calledEvents[1]
        );
    }

    public function testItShouldCallFetchAllWhenEventStops()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->orgService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.orgs', function (Event $event) use (&$result) {
            $event->stopPropagation(true);
            return $result;
        });

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.orgs',
                'target' => $this->orgService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null]
            ],
            $this->calledEvents[0]
        );
    }
}
