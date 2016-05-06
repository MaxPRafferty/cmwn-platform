<?php

namespace Asset\Service;

use Application\Exception\NotFoundException;
use Asset\AssetNotApprovedException;
use Asset\Image;
use Asset\ImageInterface;
use User\UserInterface;

/**
 * Interface UserImageServiceInterface
 */
interface UserImageServiceInterface
{

    /**
     * Saves an image to a user
     *
     * @param string|ImageInterface $image
     * @param string|UserInterface $user
     * @return bool
     */
    public function saveImageToUser($image, $user);

    /**
     * Fetches an image for a user
     *
     * @param $user
     * @param $where
     * @return Image
     * @throws AssetNotApprovedException
     * @throws NotFoundException
     */
    public function fetchImageForUser($user, $where = null);
}
