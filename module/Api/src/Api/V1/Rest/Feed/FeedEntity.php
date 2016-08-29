<?php

namespace Api\V1\Rest\Feed;

use Api\FeedEntityInterface;
use Api\Links\GameLink;
use Game\Game;

/**
 * Class FeedEntity
 * @package Api\V1\Rest\Feed
 */
class FeedEntity extends Game implements FeedEntityInterface
{
    /**
     * @inheritdoc
     */
    public function getArrayCopy()
    {
        return [
            'feed_id' => null,
            'sender' => null,
            'header' => $this->getTitle(),
            'message' => $this->getDescription(),
            'image' => null,
            'created' => $this->getCreated(),
            'type' => 'game',
            'link' => new GameLink($this->getGameId()),
        ];
    }
}
