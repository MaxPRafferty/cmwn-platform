<?php

namespace Asset\Service;

use Application\Exception\NotFoundException;
use Asset\AssetNotApprovedException;
use Asset\Image;
use Asset\ImageInterface;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class UserImageService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @param string|ImageInterface $image
     * @param string|UserInterface $user
     * @return bool
     */
    public function saveImageToUser($image, $user)
    {
        $imageId = $image instanceof ImageInterface ? $image->getImageId() : $image;
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
    public function fetchImageForUser($user, $approvedOnly = true)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $select = new Select();
        $select->columns(['i' => '*'], false);
        $select->from(['u' => $this->imageTableGateway->getTable()]);
        $select->join(['i' => 'images'], 'i.image_id = u.image_id', [], Select::JOIN_LEFT);

        $where = new Where();
        $where->addPredicate(new Operator('u.user_id', '=', $userId));
        if ($approvedOnly) {
            $where->addPredicate(new Operator('i.moderation_status', '=', 1));
        }

        $select->where($where);
        $select->order('i.created DESC');

        $rowSet = $this->imageTableGateway->selectWith($select);
        /** @var \ArrayObject|null $row */
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Image not Found for user");
        }

        return new Image($row->getArrayCopy());
    }
}
