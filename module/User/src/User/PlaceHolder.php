<?php

namespace User;

/**
 * A place holder user is just that.
 *
 * This is helpful for when you a user but only have limited information about that user (like the user id)
 */
class PlaceHolder extends User
{
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'PLACE_HOLDER';
    }
}
