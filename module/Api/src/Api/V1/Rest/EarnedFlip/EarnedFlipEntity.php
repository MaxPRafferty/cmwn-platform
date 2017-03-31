<?php

namespace Api\V1\Rest\EarnedFlip;

use Flip\EarnedFlip;
use Flip\EarnedFlipInterface;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class FlipUserEntity
 */
class EarnedFlipEntity extends EarnedFlip implements EarnedFlipInterface, LinkCollectionAwareInterface
{
    /**
     * @var LinkCollection
     */
    protected $links;

    /**
     * Sets the links when the uris are set
     *
     * @inheritdoc
     */
    public function exchangeArray(array $array): EarnedFlipInterface
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
