<?php

namespace SearchTest\Service;

use Application\Exception\NotFoundException;
use Elasticsearch\Client;
use PHPUnit\Framework\TestCase;
use Search\Service\ElasticAdapter;
use Search\SearchableDocumentInterface;
use Search\ElasticHydrator;
use Search\Service\ElasticService;
use Zend\EventManager\EventManager;
use Zend\Hydrator\ArraySerializable;
use Zend\Stdlib\ArrayObject;

/**
 * Test ElasticServiceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ElasticServiceTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|ElasticHydrator
     */
    protected $hydrator;

    /**
     * @var \Mockery\MockInterface|Client
     */
    protected $client;

    /**
     * @var ElasticService
     */
    protected $service;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = new ElasticService(
            $this->client,
            $this->hydrator,
            ['index' => 'test'],
            new EventManager()
        );
    }

    /**
     * @before
     */
    public function setUpElasticHydrator()
    {
        $this->hydrator = new ElasticHydrator([
            ElasticHydrator::class => [
                'default_hydrator' => ArraySerializable::class,
            ],
        ]);
    }

    /**
     * @before
     */
    public function setUpClient()
    {
        $this->client = \Mockery::mock(Client::class);
    }

    /**
     * @test
     */
    public function testItShouldReturnResultsWhenSearchingByType()
    {
        $this->client->shouldReceive('search')
            ->once()
            ->with([
                'index' => 'test',
                'type'  => 'foo',
                'size'  => 5,
                'body'  => [
                    'q' => 'bar*',
                ],
            ])
            ->andReturn([
                '_shards'   => [
                    'failed'     => 0,
                    'successful' => 1,
                    'total'      => 1,
                ],
                'hits'      => [
                    'hits'      => [
                        [
                            '_id'     => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                            '_index'  => 'test',
                            '_score'  => 1.0,
                            '_source' => [
                                'fizz' => 'buzz',
                            ],
                            '_type'   => 'foo',
                        ],
                    ],
                    'max_score' => 1.0,
                    'total'     => 1,
                ],
                'timed_out' => false,
                'took'      => 11,
            ]);

        $adapter = $this->service->searchByType('foo', 'bar*');
        $this->assertInstanceOf(
            ElasticAdapter::class,
            $adapter,
            ElasticService::class . ' did not return a hydrating iterator'
        );

        $results = $adapter->getItems(0, 5);
        $this->assertEquals(
            1,
            $adapter->count(),
            ElasticAdapter::class . ' is not reporting correct count'
        );

        $results->rewind();
        $this->assertEquals(
            'buzz',
            $results->current()['fizz'],
            ElasticService::class . ' did not return the expected results'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnResultsWhenSearchingByTypeWithCustomPrototype()
    {
        $this->client->shouldReceive('search')
            ->once()
            ->with([
                'index' => 'test',
                'type'  => 'foo',
                'size'  => 5,
                'body'  => [
                    'q' => 'bar*',
                ],
            ])
            ->andReturn([
                '_shards'   => [
                    'failed'     => 0,
                    'successful' => 1,
                    'total'      => 1,
                ],
                'hits'      => [
                    'hits'      => [
                        [
                            '_id'     => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                            '_index'  => 'test',
                            '_score'  => 1.0,
                            '_source' => [
                                'fizz' => 'buzz',
                            ],
                            '_type'   => 'foo',
                        ],
                    ],
                    'max_score' => 1.0,
                    'total'     => 1,
                ],
                'timed_out' => false,
                'took'      => 11,
            ]);

        $adapter = $this->service->searchByType('foo', 'bar*', new SearchPrototype());
        $results = $adapter->getItems(0, 5);
        $results->rewind();
        $this->assertEquals(
            'buzz',
            $results->current()->getData()['fizz'],
            ElasticService::class . ' did not return the expected results'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnResultsWhenSearching()
    {
        $this->client->shouldReceive('search')
            ->once()
            ->with([
                'index' => 'test',
                'size'  => 5,
                'body'  => [
                    'q' => 'bar*',
                ],
            ])
            ->andReturn([
                '_shards'   => [
                    'failed'     => 0,
                    'successful' => 1,
                    'total'      => 1,
                ],
                'hits'      => [
                    'hits'      => [
                        [
                            '_id'     => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                            '_index'  => 'test',
                            '_score'  => 1.0,
                            '_source' => [
                                'fizz' => 'buzz',
                            ],
                            '_type'   => 'foo',
                        ],
                    ],
                    'max_score' => 1.0,
                    'total'     => 1,
                ],
                'timed_out' => false,
                'took'      => 11,
            ]);

        $adapter = $this->service->search('bar*');
        $this->assertInstanceOf(
            ElasticAdapter::class,
            $adapter,
            ElasticService::class . ' did not return a hydrating iterator'
        );

        $results = $adapter->getItems(0, 5);
        $results->rewind();
        $this->assertEquals(
            'buzz',
            $results->current()['fizz'],
            ElasticService::class . ' did not return the expected results'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnResultsWhenSearchingWithCustomPrototype()
    {
        $this->client->shouldReceive('search')
            ->once()
            ->with([
                'index' => 'test',
                'size'  => 5,
                'body'  => [
                    'q' => 'bar*',
                ],
            ])
            ->andReturn([
                '_shards'   => [
                    'failed'     => 0,
                    'successful' => 1,
                    'total'      => 1,
                ],
                'hits'      => [
                    'hits'      => [
                        [
                            '_id'     => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                            '_index'  => 'test',
                            '_score'  => 1.0,
                            '_source' => [
                                'fizz' => 'buzz',
                            ],
                            '_type'   => 'foo',
                        ],
                    ],
                    'max_score' => 1.0,
                    'total'     => 1,
                ],
                'timed_out' => false,
                'took'      => 11,
            ]);

        $adapter = $this->service->search('bar*', new SearchPrototype());
        $this->assertInstanceOf(
            ElasticAdapter::class,
            $adapter,
            ElasticService::class . ' did not return a hydrating iterator'
        );

        $results = $adapter->getItems(0, 5);
        $results->rewind();
        $this->assertEquals(
            'buzz',
            $results->current()->getData()['fizz'],
            ElasticService::class . ' did not return the expected results'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchDocumentById()
    {
        $this->client->shouldReceive('get')
            ->once()
            ->with([
                'index' => 'test',
                'type'  => 'foo',
                'id'    => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
            ])
            ->andReturn([
                '_id'      => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                '_index'   => 'test',
                '_version' => 28,
                '_source'  => [
                    'fizz' => 'buzz',
                ],
                '_type'    => 'foo',
                'found'    => true,
            ]);

        $result = $this->service->fetchDocumentByTypeAndId('foo', 'a014eb36-655e-11e6-8e10-7f295bae2cce');
        $this->assertInstanceOf(
            ArrayObject::class,
            $result,
            ElasticService::class . ' did not return an ArrayObject'
        );

        $this->assertEquals(
            'buzz',
            $result['fizz'],
            ElasticService::class . ' did not return the expected results'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchDocumentByIdWithCustomPrototype()
    {
        $this->client->shouldReceive('get')
            ->once()
            ->with([
                'index' => 'test',
                'type'  => 'foo',
                'id'    => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
            ])
            ->andReturn([
                '_id'      => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                '_index'   => 'test',
                '_version' => 28,
                '_source'  => [
                    'fizz' => 'buzz',
                ],
                '_type'    => 'foo',
                'found'    => true,
            ]);

        $result = $this->service->fetchDocumentByTypeAndId(
            'foo',
            'a014eb36-655e-11e6-8e10-7f295bae2cce',
            new SearchPrototype()
        );

        $this->assertEquals(
            'buzz',
            $result->getData()['fizz'],
            ElasticService::class . ' did not return the expected results'
        );
    }

    /**
     * @test
     */
    public function testItShouldSaveADocument()
    {
        /** @var \Mockery\MockInterface|SearchableDocumentInterface $doc */
        $doc = \Mockery::mock(SearchableDocumentInterface::class);

        $doc->shouldReceive('getDocumentType')
            ->atLeast(1)
            ->andReturn('foo');

        $doc->shouldReceive('getDocumentId')
            ->atLeast(1)
            ->andReturn('bar');

        $doc->shouldReceive('getArrayCopy')
            ->atLeast(1)
            ->andReturn(['fizz' => 'buzz']);

        $this->client->shouldReceive('index')
            ->once()
            ->with([
                'index' => 'test',
                'id'    => 'bar',
                'type'  => 'foo',
                'body'  => ['fizz' => 'buzz'],
            ])
            ->andReturn([
                '_shards'  => [
                    'total'      => 10,
                    'failed'     => 0,
                    'successful' => 10,
                ],
                '_index'   => 'test',
                '_type'    => 'foo',
                '_id'      => 'bar',
                '_version' => 1,
                'created'  => true,
            ]);

        $this->assertTrue(
            $this->service->saveDocument($doc),
            ElasticService::class . ' did not return boolean on save'
        );
    }

    /**
     * @test
     */
    public function testItShouldDeleteADocument()
    {
        /** @var \Mockery\MockInterface|SearchableDocumentInterface $doc */
        $doc = \Mockery::mock(SearchableDocumentInterface::class);

        $doc->shouldReceive('getDocumentType')
            ->atLeast(1)
            ->andReturn('foo');

        $doc->shouldReceive('getDocumentId')
            ->atLeast(1)
            ->andReturn('bar');

        $doc->shouldReceive('getArrayCopy')
            ->atLeast(1)
            ->andReturn(['fizz' => 'buzz']);

        $this->client->shouldReceive('delete')
            ->once()
            ->with([
                'index' => 'test',
                'id'    => 'bar',
                'type'  => 'foo',
            ])
            ->andReturn([
                '_shards'  => [
                    'total'      => 10,
                    'failed'     => 0,
                    'successful' => 10,
                ],
                '_index'   => 'test',
                '_type'    => 'foo',
                '_id'      => 'bar',
                '_version' => 1,
                'found'    => true,
            ]);

        $this->assertTrue(
            $this->service->deleteDocument($doc),
            ElasticService::class . ' did not return boolean on delete'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenGettingDocumentIsNotFound()
    {
        $this->client->shouldReceive('get')
            ->once()
            ->with([
                'index' => 'test',
                'type'  => 'foo',
                'id'    => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
            ])
            ->andReturn([
                '_id'      => 'a014eb36-655e-11e6-8e10-7f295bae2cce',
                '_index'   => 'test',
                '_version' => 28,
                '_source'  => [],
                'found'    => false,
            ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Could not find a document of type foo by id a014eb36-655e-11e6-8e10-7f295bae2cce'
        );

        $this->service->fetchDocumentByTypeAndId('foo', 'a014eb36-655e-11e6-8e10-7f295bae2cce');
    }
}
