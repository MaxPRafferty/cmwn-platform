<?php

namespace AssetTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Asset\Image;
use Asset\Service\ImageService;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Predicate as Where;

/**
 * Test ImageServiceTest
 * @group Image
 * @group Asset
 * @group Service
 * @group ImageService
 */
class ImageServiceTest extends TestCase
{
    /**
    * @var ImageService
    */
    protected $imageService;

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
        $this->tableGateway->shouldReceive('getTable')->andReturn('images')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->imageService = new ImageService($this->tableGateway);
    }

    /**
     * @test
     */
    public function testItShouldReturnPaginatingAdapterByDefaultOnFetchAll()
    {
        $this->tableGateway
            ->shouldReceive('select')
            ->never();

        $result = $this->imageService->fetchAll(null);
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

        $result = $this->imageService->fetchAll(null, false);
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

        $result = $this->imageService->fetchAll($expectedWhere, false);
        $this->assertInstanceOf('\Iterator', $result);
    }

    /**
     * @test
     */
    public function testItShouldSaveNewImage()
    {
        $newImage = new Image();

        $this->assertNull($newImage->getCreated());
        $this->assertNull($newImage->getUpdated());
        $this->assertEmpty($newImage->getImageId());

        $this->tableGateway->shouldReceive('insert')
            ->andReturnUsing(function ($data) use (&$newImage) {
                $this->assertNotNull($newImage->getCreated());
                $this->assertNotNull($newImage->getUpdated());

                $this->assertTrue(is_array($data));

                $expected = $newImage->getArrayCopy();
                unset($expected['is_moderated']);
                unset($expected['deleted']);
                $expected['moderation_status'] = 0;
                $this->assertArrayNotHasKey('deleted', $data);
                $this->assertEquals($expected, $data);
                return 1;
            })
            ->once();

        $this->assertTrue($this->imageService->saveNewImage($newImage));
    }

    /**
     * @test
     */
    public function testItShouldUpdateExistingImage()
    {
        $imageData = [
            'image_id'     => 'image_id',
            'url'          => 'http://www.manchuck.com',
            'is_moderated' => true,
            'type'         => 'image/png',
            'created'      => '2016-02-28',
            'updated'      => '2016-02-28',
            'deleted'      => '2016-02-28',
        ];

        $image   = new Image($imageData);
        $image->setModerationStatus(1);
        $result = new ResultSet();
        $result->initialize([$imageData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['image_id' => $imageData['image_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$image) {
                $this->assertEquals(['image_id' => $image->getImageId()], $where);
                $expected = $image->getArrayCopy();
                unset($expected['is_moderated']);
                unset($expected['deleted']);
                $expected['moderation_status'] = -1;
                $this->assertArrayNotHasKey('deleted', $data);

                $this->assertEquals($expected, $data);
            });

        $this->assertTrue($this->imageService->saveImage($image));
    }

    /**
     * @test
     */
    public function testItShouldFetchImageById()
    {
        $imageData = [
            'image_id'     => 'image_id',
            'url'          => 'http://www.manchuck.com',
            'is_moderated' => true,
            'type'         => 'image/png',
            'created'      => '2016-02-28',
            'updated'      => '2016-02-28',
            'deleted'      => '2016-02-28',
        ];

        $result = new ResultSet();
        $result->initialize([$imageData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['image_id' => $imageData['image_id']])
            ->andReturn($result);

        $this->assertInstanceOf('Asset\Image', $this->imageService->fetchImage($imageData['image_id']));
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenImageIsNotFound()
    {
        $this->setExpectedException(
            'Application\Exception\NotFoundException',
            'Image not Found'
        );

        $result = new ResultSet();
        $result->initialize([]);
        $this->tableGateway->shouldReceive('select')
            ->andReturn($result);

        $this->imageService->fetchImage('foo');
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteByDefault()
    {
        $imageData = [
            'image_id'     => 'image_id',
            'url'          => 'http://www.manchuck.com',
            'is_moderated' => true,
            'type'         => 'image/png',
            'created'      => '2016-02-28',
            'updated'      => '2016-02-28',
            'deleted'      => '',
        ];

        $image   = new Image($imageData);
        $result = new ResultSet();
        $result->initialize([$imageData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['image_id' => $imageData['image_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('update')
            ->andReturnUsing(function ($data, $where) use (&$image) {
                $this->assertEquals(['image_id' => $image->getImageId()], $where);
                $this->assertNotEmpty($data['deleted']);
            });

        $this->assertTrue($this->imageService->deleteImage($image));
    }

    /**
     * @test
     */
    public function testItShouldSoftDeleteWhenForced()
    {
        $imageData = [
            'image_id'     => 'image_id',
            'url'          => 'http://www.manchuck.com',
            'is_moderated' => true,
            'type'         => 'image/png',
            'created'      => '2016-02-28',
            'updated'      => '2016-02-28',
            'deleted'      => '',
        ];

        $image   = new Image($imageData);
        $result = new ResultSet();
        $result->initialize([$imageData]);
        $this->tableGateway->shouldReceive('select')
            ->with(['image_id' => $imageData['image_id']])
            ->andReturn($result);

        $this->tableGateway->shouldReceive('delete')
            ->andReturnUsing(function ($where) use (&$image) {
                $this->assertEquals(['image_id' => $image->getImageId()], $where);
            });

        $this->assertTrue($this->imageService->deleteImage($image, false));
    }
}
