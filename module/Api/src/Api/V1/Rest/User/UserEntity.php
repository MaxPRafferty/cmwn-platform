<?php

namespace Api\V1\Rest\User;

use Api\Links\GameLink;
use User\User;
use User\UserInterface;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class UserEntity
 *
 * @package Api\V1\Rest\User
 */
class UserEntity extends User implements UserInterface, LinkCollectionAwareInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var LinkCollection
     */
    protected $links;

    /**
     * @return mixed
     */
    public function getArrayCopy()
    {
        return array_merge(
            parent::getArrayCopy(),
            ['links' => $this->getLinks()]
        );
    }

    /**
     * @param $type
     */
    protected function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set link collection
     *
     * @param  LinkCollection $links
     * @return self
     */
    public function setLinks(LinkCollection $links)
    {
        $this->links = $links;
        $this->links->add(new GameLink());
        return $this;
    }

    /**
     * Get link collection
     *
     * @return LinkCollection
     */
    public function getLinks()
    {
        if (!$this->links instanceof LinkCollection) {
            $this->setLinks(new LinkCollection());
        }

        return $this->links;
    }
}
