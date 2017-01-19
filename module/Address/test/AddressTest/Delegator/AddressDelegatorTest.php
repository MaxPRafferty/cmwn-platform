<?php

namespace AddressTest\Delegator;

use Address\Address;
use Address\Delegator\AddressDelegator;
use Address\Service\AddressService;
use Application\Exception\NotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class AddressDelegatorTest
 * @package AddressTest\Delegator
 * @SuppressWarnings(PHPMD)
 */
class AddressDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|AddressService
     */
    protected $addressService;

    /**
     * @var \Mockery\MockInterface|Adapter
     */
    protected $adapter;

    /**
     * @var AddressDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @before
     */
    public function setUpService()
    {
        $this->addressService = \Mockery::mock(AddressService::class);
    }

    /**
     * @before
     */
    public function setUpAdapter()
    {
        $this->adapter = \Mockery::mock(Adapter::class);
        $this->adapter->shouldReceive('getPlatform')->byDefault();
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $events             = new EventManager();
        $this->calledEvents = [];
        $this->delegator    = new AddressDelegator($this->addressService, $events);
        $events->attach('*', [$this, 'captureEvents'], PHP_INT_MAX);
    }

    /**
     * @param Event $event
     */
    public function captureEvents(Event $event)
    {
        $this->calledEvents[] = [
            'name'   => $event->getName(),
            'target' => $event->getTarget(),
            'params' => $event->getParams(),
        ];
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAll()
    {
        $dbSelect = new DbSelect(new Select(), $this->adapter, new HydratingResultSet());
        $this->addressService->shouldReceive('fetchAll')
            ->andReturn($dbSelect)->once();

        $this->delegator->fetchAll();

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events when fetching all'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.addresses',
                'target' => $this->addressService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.all.addresses'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.addresses.post',
                'target' => $this->addressService,
                'params' => ['where' => null, 'prototype' => null, 'addresses' => $dbSelect],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.all.addresses.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAllAndTriggerEventOnError()
    {
        $exception = new \Exception();
        $this->addressService->shouldReceive('fetchAll')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchAll();
            $this->fail(AddressDelegator::class . ' exception was not thrown with fetchAll');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                AddressDelegator::class . ' did not re-throw the same exception'
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events when fetching all with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.addresses',
                'target' => $this->addressService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.all.addresses with error'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.all.addresses.error',
                'target' => $this->addressService,
                'params' => ['where' => null, 'prototype' => null, 'exception' => new \Exception()],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.all.addresses with error'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllWhenEventStops()
    {
        $result = new DbSelect(new Select(), $this->adapter, new HydratingResultSet());
        $this->addressService->shouldReceive('fetchAll')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.addresses', function (Event $event) use (&$result) {
            $event->stopPropagation(true);

            return $result;
        });

        $this->assertSame(
            $result,
            $this->delegator->fetchAll(),
            AddressDelegator::class . ' did not return the event response'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.addresses',
                'target' => $this->addressService,
                'params' => ['where' => null, 'prototype' => null],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.all.addresses'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAddress()
    {
        $result = new Address(['address_id' => 'foo']);
        $this->addressService->shouldReceive('fetchAddress')
            ->andReturn($result)
            ->once();

        $this->delegator->fetchAddress('foo-bar');

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events for fetchFlipById'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.address',
                'target' => $this->addressService,
                'params' => ['address_id' => 'foo-bar', 'prototype' => null],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.address'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.address.post',
                'target' => $this->addressService,
                'params' => ['address_id' => 'foo-bar', 'prototype' => null, 'address' => $result],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.address.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAddressAndTriggerError()
    {
        $exception = new NotFoundException();
        $this->addressService->shouldReceive('fetchAddress')
            ->andThrow($exception)
            ->once();

        try {
            $this->delegator->fetchAddress('foo-bar');
            $this->fail(AddressDelegator::class . ' exception was not thrown with fetchAddress');
        } catch (\Throwable $actual) {
            $this->assertSame(
                $exception,
                $actual,
                AddressDelegator::class . ' did not re-throw the same exception'
            );
        }

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events for fetchAddress with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.address',
                'target' => $this->addressService,
                'params' => ['address_id' => 'foo-bar', 'prototype' => null],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.address with error'
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.address.error',
                'target' => $this->addressService,
                'params' => ['address_id' => 'foo-bar', 'prototype' => null, 'exception' => $exception],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.address.error'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallNotFetchAddressWhenEventStops()
    {
        $address = new Address([]);
        $this->addressService->shouldReceive('fetchAddress')
            ->never();

        $this->delegator->getEventManager()->attach('fetch.address', function (Event $event) use (&$address) {
            $event->stopPropagation(true);

            return $address;
        });

        $this->assertEquals(
            $address,
            $this->delegator->fetchAddress('foo-bar'),
            AddressDelegator::class . ' did not return false from the event'
        );

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events when fetch.address is stopped'
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.address',
                'target' => $this->addressService,
                'params' => ['address_id' => 'foo-bar', 'prototype' => null],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly for fetch.address when stopped'
        );
    }

    /**
     * @test
     */
    public function testItShouldCallCreateAddress()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->addressService->shouldReceive('createAddress')
            ->andReturn(true)->once();

        $this->delegator->createAddress($address);

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'create.address',
                'target' => $this->addressService,
                'params' => ['address' => $address],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'create.address.post',
                'target' => $this->addressService,
                'params' => ['address' => $address, 'return' => true],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for create.address.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallCreateAddressWhenEventStops()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->delegator->getEventManager()->attach('create.address', function (Event $event) use (&$address) {
            $event->stopPropagation(true);

            return false;
        });

        $this->delegator->createAddress($address);

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'create.address',
                'target' => $this->addressService,
                'params' => ['address' => $address],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventWhenExceptionIsThrownOnCreateAddress()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->addressService->shouldReceive('createAddress')
            ->andReturnUsing(function () {
                throw new \Exception();
            })->once();

        try {
            $this->delegator->createAddress($address);
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                AddressDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name' => 'create.address',
                    'target' => $this->addressService,
                    'params' => ['address' => $address],
                ],
                $this->calledEvents[0],
                AddressDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name' => 'create.address.error',
                    'target' => $this->addressService,
                    'params' => ['address' => $address, 'exception' => new \Exception()],
                ],
                $this->calledEvents[1],
                AddressDelegator::class . ' did not trigger the event correctly for create.address.error'
            );
        }
    }

    /**
     * @test
     */
    public function testItShouldCallUpdateAddress()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->addressService->shouldReceive('updateAddress')
            ->andReturn(true)->once();

        $this->delegator->updateAddress($address);

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'update.address',
                'target' => $this->addressService,
                'params' => ['address' => $address],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'update.address.post',
                'target' => $this->addressService,
                'params' => ['address' => $address, 'return' => true],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for update.address.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallUpdateAddressWhenEventStops()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->delegator->getEventManager()->attach('update.address', function (Event $event) use (&$address) {
            $event->stopPropagation(true);

            return false;
        });

        $this->delegator->updateAddress($address);

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'update.address',
                'target' => $this->addressService,
                'params' => ['address' => $address],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventOnExceptionWhenUpdateAddressIsCalled()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->addressService->shouldReceive('updateAddress')
            ->andReturn(true)->once();

        try {
            $this->delegator->updateAddress($address);
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                AddressDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name' => 'update.address',
                    'target' => $this->addressService,
                    'params' => ['address' => $address],
                ],
                $this->calledEvents[0],
                AddressDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name' => 'update.address.error',
                    'target' => $this->addressService,
                    'params' => ['address' => $address, 'exception' => new \Exception()],
                ],
                $this->calledEvents[1],
                AddressDelegator::class . ' did not trigger the event correctly for update.address.error'
            );
        }
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteAddress()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->addressService->shouldReceive('deleteAddress')
            ->andReturn(true)->once();

        $this->delegator->deleteAddress($address);

        $this->assertEquals(
            2,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.address',
                'target' => $this->addressService,
                'params' => ['address' => $address],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.address.post',
                'target' => $this->addressService,
                'params' => ['address' => $address, 'return' => true],
            ],
            $this->calledEvents[1],
            AddressDelegator::class . ' did not trigger the event correctly for delete.address.post'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteAddressWhenEventStops()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->delegator->getEventManager()->attach('delete.address', function (Event $event) use (&$address) {
            $event->stopPropagation(true);

            return false;
        });

        $this->delegator->deleteAddress($address);

        $this->assertEquals(
            1,
            count($this->calledEvents),
            AddressDelegator::class . ' did not trigger the correct number of events'
        );

        $this->assertEquals(
            [
                'name'   => 'delete.address',
                'target' => $this->addressService,
                'params' => ['address' => $address],
            ],
            $this->calledEvents[0],
            AddressDelegator::class . ' did not trigger the event correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldTriggerErrorEventWhenExceptionIsThrownOnDeleteAddress()
    {
        $address = new Address([
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $this->addressService->shouldReceive('deleteAddress')
            ->andReturnUsing(function () {
                throw new \Exception();
            })->once();

        try {
            $this->delegator->deleteAddress($address);
            $this->fail('exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals(
                2,
                count($this->calledEvents),
                AddressDelegator::class . ' did not trigger the correct number of events'
            );

            $this->assertEquals(
                [
                    'name' => 'delete.address',
                    'target' => $this->addressService,
                    'params' => ['address' => $address],
                ],
                $this->calledEvents[0],
                AddressDelegator::class . ' did not trigger the event correctly'
            );

            $this->assertEquals(
                [
                    'name' => 'delete.address.error',
                    'target' => $this->addressService,
                    'params' => ['address' => $address, 'exception' => new \Exception()],
                ],
                $this->calledEvents[1],
                AddressDelegator::class . ' did not trigger the event correctly for delete.address.error'
            );
        }
    }
}
