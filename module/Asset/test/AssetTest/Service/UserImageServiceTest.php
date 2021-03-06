<?php

namespace AssetTest\Service;

use Application\Exception\NotFoundException;
use Asset\Image;
use Asset\Service\UserImageService;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Adult;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

/**
 * Exception UserImageServiceTest
 *
 * @group Asset
 * @group Image
 * @group User
 * @group Service
 * @group UserImageService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserImageServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var UserImageService
     */
    protected $service;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = new UserImageService($this->tableGateway);
    }

    /**
     * @before
     */
    public function setUpGateway()
    {
        /** @var \Mockery\MockInterface|\Zend\Db\Adapter\AdapterInterface $adapter */
        $adapter = \Mockery::mock('\Zend\Db\Adapter\Adapter');
        $adapter->shouldReceive('getPlatform')->byDefault();

        $this->tableGateway = \Mockery::mock('\Zend\Db\TableGateway\TableGateway');
        $this->tableGateway->shouldReceive('getTable')->andReturn('user_images')->byDefault();
        $this->tableGateway->shouldReceive('getAdapter')->andReturn($adapter)->byDefault();
    }

    /**
     * @test
     */
    public function testItShouldSaveImageToUserWithIds()
    {
        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->with(['image_id' => 'foo', 'user_id' => 'bar']);

        $this->assertTrue(
            $this->service->saveImageToUser('foo', 'bar'),
            'User Image service did not save image to user with Id'
        );
    }

    /**
     * @test
     */
    public function testItShouldSaveImageToUserWithObjects()
    {
        $image = new Image();
        $image->setImageId('foo');

        $user = new Adult();
        $user->setUserId('bar');

        $this->tableGateway->shouldReceive('insert')
            ->once()
            ->with(['image_id' => 'foo', 'user_id' => 'bar']);

        $this->assertTrue(
            $this->service->saveImageToUser($image, $user),
            'User Image service did not save image to user with objects'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchImageUsingId()
    {
        $expectedImage = new Image();
        $expectedImage->setModerated(true);
        $this->tableGateway->shouldReceive('selectWith')
            ->once()
            ->andReturnUsing(function ($actual) use (&$expectedImage) {

                $select = new Select();
                $select->columns(['i' => '*'], false);
                $select->from(['u' => 'user_images']);
                $select->join(['i' => 'images'], 'i.image_id = u.image_id', [], Select::JOIN_LEFT);

                $where = new Where();
                $where->addPredicate(new Operator('u.user_id', '=', 'foo'));
                $where->addPredicate(new Operator('i.moderation_status', Operator::OP_NE, -1));
                $where->addPredicate(new Operator('i.moderation_status', '=', 1));
                $select->where($where);
                $select->order('i.created DESC');
                $this->assertEquals(
                    $select,
                    $actual,
                    'User Image Service did not build correct select'
                );

                return new \ArrayIterator([new \ArrayObject($expectedImage->getArrayCopy())]);
            });

        $actualImage = $this->service->fetchImageForUser('foo');

        $this->assertEquals(
            $expectedImage,
            $actualImage,
            'Image was not returned from service'
        );
    }

    /**
     * @test
     */
    public function testItShouldSetModerationStatusZeroWhenApprovedOnlyIsFalse()
    {
        $expectedImage = new Image();
        $this->tableGateway->shouldReceive('selectWith')
            ->once()
            ->andReturnUsing(function ($actual) use (&$expectedImage) {

                $select = new Select();
                $select->columns(['i' => '*'], false);
                $select->from(['u' => 'user_images']);
                $select->join(['i' => 'images'], 'i.image_id = u.image_id', [], Select::JOIN_LEFT);

                $where = new Where();
                $where->addPredicate(new Operator('u.user_id', '=', 'foo'));
                $where->addPredicate(new Operator('i.moderation_status', Operator::OP_NE, -1));
                $select->where($where);
                $select->order('i.created DESC');

                $this->assertEquals(
                    $select,
                    $actual,
                    'User Image Service did not build correct select'
                );

                return new \ArrayIterator([new \ArrayObject($expectedImage->getArrayCopy())]);
            });

        $actualImage = $this->service->fetchImageForUser('foo', false);

        $this->assertEquals(
            $expectedImage,
            $actualImage,
            'Image was not returned from service'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowNotFoundExceptionWhenImageNotFound()
    {
        $this->tableGateway->shouldReceive('selectWith')
            ->once()
            ->andReturnUsing(function ($actual) {

                $select = new Select();
                $select->columns(['i' => '*'], false);
                $select->from(['u' => 'user_images']);
                $select->join(['i' => 'images'], 'i.image_id = u.image_id', [], Select::JOIN_LEFT);

                $where = new Where();
                $where->addPredicate(new Operator('u.user_id', '=', 'foo'));
                $where->addPredicate(new Operator('i.moderation_status', Operator::OP_NE, -1));
                $where->addPredicate(new Operator('i.moderation_status', '=', 1));
                $select->where($where);
                $select->order('i.created DESC');
                $this->assertEquals(
                    $select,
                    $actual,
                    'User Image Service did not build correct select'
                );

                return new \ArrayIterator([]);
            });

        $this->expectException(NotFoundException::class);
        $this->service->fetchImageForUser('foo');
    }
}
