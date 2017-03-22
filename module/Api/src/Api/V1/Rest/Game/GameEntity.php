<?php

namespace Api\V1\Rest\Game;

use Game\Game;
use Game\GameInterface;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * A Game Entity represents the game through the API
 *
 * @SWG\Definition(
 *     definition="GameEntity",
 *     description="A Game Entity represents the game through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the game might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Game"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class GameEntity extends Game implements GameInterface, LinkCollectionAwareInterface
{
    /**
     * @var LinkCollection
     */
    protected $links;

    /**
     * Sets the links when the uris are set
     *
     * @inheritdoc
     * @todo possibly remove links when uris are set maybe?
     */
    public function exchangeArray(array $array): GameInterface
    {
        parent::exchangeArray($array);

        foreach ($this->getUris() as $rel => $href) {
            $this->getLinks()->remove($rel);
            $this->getLinks()->add(
                Link::factory([
                    'rel' => $rel,
                    'url' => $href,
                ])
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        $array = parent::getArrayCopy();

        // Dont pass these back up through the API
        unset($array['flags']);
        unset($array['uris']);

        $array['_links'] = $this->getLinks();
        return $array;
    }

    /**
     * @inheritDoc
     */
    public function setLinks(LinkCollection $links)
    {
        $this->links = $links;
    }

    /**
     * @inheritDoc
     */
    public function getLinks()
    {
        if (empty($this->links)) {
            $this->setLinks(new LinkCollection());
        }

        return $this->links;
    }
}
