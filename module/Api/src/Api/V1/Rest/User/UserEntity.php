<?php

namespace Api\V1\Rest\User;

use Api\Links\ForgotLink;
use Api\Links\GameLink;
use Api\Links\ProfileLink;
use Api\Links\UserImageLink;
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
        return $this;
    }

    /**
     * @param string $userId
     * @return User
     */
    public function setUserId($userId)
    {
        if (empty($this->userId) && !empty($userId)) {
            $this->getLinks()->add(new GameLink());
            $this->getLinks()->add(new ForgotLink());
            $this->getLinks()->add(new ProfileLink($userId));
            $this->getLinks()->add(new UserImageLink($userId));
        }

        return parent::setUserId($userId); // TODO: Change the autogenerated stub
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