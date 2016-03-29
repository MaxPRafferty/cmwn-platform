<?php

namespace Asset\Service;

use Application\Exception\NotFoundException;
use Asset\AssetNotApprovedException;
use Asset\Image;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class UserImageService
 */
class UserImageService implements UserImageServiceInterface
{
    /**
     * @var TableGateway
     */
    protected $imageTableGateway;

    /**
     * UserImageService constructor.
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->imageTableGateway = $gateway;
    }

    /**
     * Saves an image to a user
     *
     * @param $image
     * @param $user
     * @return bool
     */
    public function saveImageToUser($image, $user)
    {
        $imageId = $image instanceof Image ? $image->getImageId() : $image;
        $userId  = $user instanceof UserInterface ? $user->getUserId() : $user;

        $this->imageTableGateway->insert(['user_id' => $userId, 'image_id' => $imageId]);
        return true;
    }

    /**
     * Fetches an image for a user
     *
     * @param $user
     * @return Image
     * @throws AssetNotApprovedException
     * @throws NotFoundException
     */
    public function fetchImageForUser($user)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $select = new Select();
        $select->columns(['i' => '*'], false);
        $select->from($this->imageTableGateway->getTable());
        $select->join(['i' => 'images'], 'i.image_id = u.image_id', [], Select::JOIN_LEFT);

        $where = new Where();
        $where->addPredicate(new Operator('u.user_id', '=', $userId));
        $select->where($where);

        $rowset = $this->imageTableGateway->selectWith($select);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("Image not Found for user");
        }

        $image = new Image($row->getArrayCopy());
        
        if (!$image->isModerated()) {
            throw new AssetNotApprovedException($image);
        }

        return $image;
    }
}
