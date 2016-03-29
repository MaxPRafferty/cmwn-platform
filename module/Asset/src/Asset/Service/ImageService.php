<?php

namespace Asset\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Asset\Image;
use Asset\ImageInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class ImageService
 * @package Asset\Service
 */
class ImageService implements ImageServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $imageTableGateway;

    public function __construct(TableGateway $gateway)
    {
        $this->imageTableGateway = $gateway;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select    = new Select($this->imageTableGateway->getTable());
            $select->where($where);
            return new DbSelect(
                $select,
                $this->imageTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->imageTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * Saves a image
     *
     * If the image id is null, then a new image is created
     *
     * @param ImageInterface $image
     * @return bool
     * @throws NotFoundException
     */
    public function saveNewImage(ImageInterface $image)
    {
        $image->setCreated(new \DateTime());
        $image->setUpdated(new \DateTime());
        $data = $image->getArrayCopy();

        $data['moderation_status'] = (int) $image->isModerated();
        unset($data['is_moderated']);
        unset($data['deleted']);

        $this->imageTableGateway->insert($data);
        return true;
    }

    /**
     * Saves a image
     *
     * If the image id is null, then a new image is created
     *
     * @param ImageInterface $image
     * @return bool
     * @throws NotFoundException
     */
    public function saveImage(ImageInterface $image)
    {
        $image->setUpdated(new \DateTime());
        $data = $image->getArrayCopy();

        $data['moderation_status'] = (int) $image->isModerated();
        unset($data['is_moderated']);
        unset($data['deleted']);

        $this->imageTableGateway->update(
            $data,
            ['image_id' => $image->getImageId()]
        );

        return true;
    }

    /**
     * Fetches one image from the DB using the id
     *
     * @param $imageId
     * @return ImageInterface
     * @throws NotFoundException
     */
    public function fetchImage($imageId)
    {
        $rowset = $this->imageTableGateway->select(['image_id' => $imageId]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("Image not Found");
        }

        return new Image($row->getArrayCopy());
    }

    /**
     * Deletes a image from the database
     *
     * Soft deletes unless soft is false
     *
     * @param ImageInterface $image
     * @param bool $soft
     * @return bool
     */
    public function deleteImage(ImageInterface $image, $soft = true)
    {
        $this->fetchImage($image->getImageId());

        if ($soft) {
            $image->setDeleted(new \DateTime());

            $this->imageTableGateway->update(
                ['deleted' => $image->getDeleted()->format(\DateTime::ISO8601)],
                ['image_id' => $image->getImageId()]
            );

            return true;
        }

        $this->imageTableGateway->delete(['image_id' => $image->getImageId()]);
        return true;
    }
}
