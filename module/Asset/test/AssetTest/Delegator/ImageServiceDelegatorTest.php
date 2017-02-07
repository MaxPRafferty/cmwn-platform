<?php

namespace AssetTest\Delegator;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \PHPUnit_Framework_TestCase as TestCase;
use Asset\Image;
use Asset\Delegator\ImageServiceDelegator;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;

/**
 * Test ImageServiceDelegatorTest
 *
 * @group Image
 * @group Delegator
 * @group Asset
 * @group ImageDelegator
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ImageServiceDelegatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Asset\Service\ImageService
     */
    protected $imageService;

    /**
     * @var ImageServiceDelegator
     */
    protected $delegator;

    /**
     * @var array
     */
    protected $calledEvents = [];

    /**
     * @var Image
     */
    protected $image;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->imageService = \Mockery::mock('\Asset\Service\ImageService');
    }

    /**
     * @before
     */
    public function setUpDelegator()
    {
        $this->calledEvents = [];
        $this->delegator    = new ImageServiceDelegator($this->imageService);
        $this->delegator->getEventManager()->clearListeners('save.image');
        $this->delegator->getEventManager()->clearListeners('fetch.image.post');
        $this->delegator->getEventManager()->clearListeners('fetch.all.images');
        $this->delegator->getEventManager()->attach('*', [$this, 'captureEvents'], 1000000);
    }

    /**
     * @before
     */
    public function setUpImage()
    {
        $this->image = new Image();
        $this->image->setImageId(md5('foobar'));
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
    public function testItShouldCallSaveImage()
    {
        $this->imageService->shouldReceive('saveImage')
            ->with($this->image)
            ->andReturn(true)
            ->once();

        $this->delegator->saveImage($this->image);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.image',
                'target' => $this->imageService,
                'params' => ['image' => $this->image],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.image.post',
                'target' => $this->imageService,
                'params' => ['image' => $this->image],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallSaveNewImage()
    {
        $this->imageService->shouldReceive('saveNewImage')
            ->with($this->image)
            ->andReturn(true)
            ->once();

        $this->delegator->saveNewImage($this->image);

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.new.image',
                'target' => $this->imageService,
                'params' => ['image' => $this->image],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'save.new.image.post',
                'target' => $this->imageService,
                'params' => ['image' => $this->image],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallSaveImageWhenEventPrevents()
    {
        $this->imageService->shouldReceive('saveImage')
            ->with($this->image)
            ->never();

        $this->delegator->getEventManager()->attach('save.image', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->saveImage($this->image));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.image',
                'target' => $this->imageService,
                'params' => ['image' => $this->image],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallSaveNewImageWhenEventPrevents()
    {
        $this->imageService->shouldReceive('saveNewImage')
            ->with($this->image)
            ->never();

        $this->delegator->getEventManager()->attach('save.new.image', function (Event $event) {
            $event->stopPropagation(true);

            return ['foo' => 'bar'];
        });

        $this->assertEquals(['foo' => 'bar'], $this->delegator->saveNewImage($this->image));

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'save.new.image',
                'target' => $this->imageService,
                'params' => ['image' => $this->image],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchImage()
    {
        $this->imageService->shouldReceive('fetchImage')
            ->with($this->image->getImageId())
            ->andReturn($this->image)
            ->once();

        $this->assertSame(
            $this->image,
            $this->delegator->fetchImage($this->image->getImageId())
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.image',
                'target' => $this->imageService,
                'params' => ['image_id' => $this->image->getImageId()],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'fetch.image.post',
                'target' => $this->imageService,
                'params' => ['image' => $this->image, 'image_id' => $this->image->getImageId()],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchImageAndReturnEventResult()
    {
        $this->imageService->shouldReceive('fetchImage')
            ->with($this->image->getImageId())
            ->andReturn($this->image)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.image', function (Event $event) {
            $event->stopPropagation(true);

            return $this->image;
        });

        $this->assertSame(
            $this->image,
            $this->delegator->fetchImage($this->image->getImageId())
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.image',
                'target' => $this->imageService,
                'params' => ['image_id' => $this->image->getImageId()],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallDeleteImage()
    {
        $this->imageService->shouldReceive('deleteImage')
            ->with($this->image, true)
            ->andReturn($this->image)
            ->once();

        $this->assertSame(
            $this->image,
            $this->delegator->deleteImage($this->image)
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.image',
                'target' => $this->imageService,
                'params' => ['image' => $this->image, 'soft' => true],
            ],
            $this->calledEvents[0]
        );
        $this->assertEquals(
            [
                'name'   => 'delete.image.post',
                'target' => $this->imageService,
                'params' => ['image' => $this->image, 'soft' => true],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallDeleteImageAndReturnEventResult()
    {
        $this->imageService->shouldReceive('deleteImage')
            ->with($this->image, true)
            ->andReturn($this->image)
            ->never();

        $this->delegator->getEventManager()->attach('delete.image', function (Event $event) {
            $event->stopPropagation(true);

            return $this->image;
        });

        $this->assertSame(
            $this->image,
            $this->delegator->deleteImage($this->image)
        );

        $this->assertEquals(1, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'delete.image',
                'target' => $this->imageService,
                'params' => ['image' => $this->image, 'soft' => true],
            ],
            $this->calledEvents[0]
        );
    }

    /**
     * @test
     */
    public function testItShouldCallFetchAll()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->imageService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->once();

        $this->assertSame(
            $result,
            $this->delegator->fetchAll()
        );

        $this->assertEquals(2, count($this->calledEvents));
        $this->assertEquals(
            [
                'name'   => 'fetch.all.images',
                'target' => $this->imageService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );

        $this->assertEquals(
            [
                'name'   => 'fetch.all.images.post',
                'target' => $this->imageService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null, 'images' => $result],
            ],
            $this->calledEvents[1]
        );
    }

    /**
     * @test
     */
    public function testItShouldNotCallFetchAllWhenEventStops()
    {
        $result = new \ArrayIterator([['foo' => 'bar']]);
        $this->imageService->shouldReceive('fetchAll')
            ->andReturn($result)
            ->never();

        $this->delegator->getEventManager()->attach('fetch.all.images', function (Event $event) use (&$result) {
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
                'name'   => 'fetch.all.images',
                'target' => $this->imageService,
                'params' => ['where' => new Where(), 'paginate' => true, 'prototype' => null],
            ],
            $this->calledEvents[0]
        );
    }
}
