<?php

namespace Asset\Service;

use Application\Exception\NotFoundException;
use Asset\AssetNotApprovedException;
use Asset\Image;

/**
 * Interface UserImageServiceInterface
 */
interface UserImageServiceInterface
{

    /**
     * Saves an image to a user
     *
     * @param $image
     * @param $user
     * @return bool
     */
    public function saveImageToUser($image, $user);

    /**
     * Fetches an image for a user
     *
     * @param $user
     * @return Image
     * @throws AssetNotApprovedException
     * @throws NotFoundException
     */
    public function fetchImageForUser($user);
}
