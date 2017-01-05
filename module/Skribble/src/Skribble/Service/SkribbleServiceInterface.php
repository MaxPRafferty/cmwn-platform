<?php

namespace Skribble\Service;

use Application\Exception\NotFoundException;
use Skribble\SkribbleInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface SkribbleServiceInterface
 */
interface SkribbleServiceInterface
{

    /**
     * Fetches all the Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchAllForUser($user, $where = null, $prototype = null);

    /**
     * Fetches all Received Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchReceivedForUser($user, $where = null, $prototype = null);

    /**
     * Fetches all Sent Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchSentForUser($user, $where = null, $prototype = null);

    /**
     * Fetches all Draft Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchDraftForUser($user, $where = null, $prototype = null);

    /**
     * Fetches a skribble from the db
     *
     * @param $skribbleId
     * @param null $prototype
     *
     * @return null|SkribbleInterface
     * @throws NotFoundException
     */
    public function fetchSkribble($skribbleId, $prototype = null);

    /**
     * Creates a new Skribble
     *
     * @param SkribbleInterface $skribble
     *
     * @return bool
     */
    public function createSkribble(SkribbleInterface $skribble);

    /**
     * Updates a skribble
     *
     * @param SkribbleInterface $skribble
     *
     * @return bool
     */
    public function updateSkribble(SkribbleInterface $skribble);

    /**
     * Deletes the Skribble
     *
     * @param SkribbleInterface|string $skribble
     * @param bool $hard
     *
     * @return int
     */
    public function deleteSkribble($skribble, $hard = false);
}
