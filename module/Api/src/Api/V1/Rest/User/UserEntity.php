<?php

namespace Api\V1\Rest\User;

use Api\Links\FlipLink;
use Api\Links\ForgotLink;
use Api\Links\GameLink;
use Api\Links\PasswordLink;
use Api\Links\ProfileLink;
use Api\Links\ResetLink;
use Api\Links\UserFlipLink;
use Api\Links\UserImageLink;
use Api\ScopeAwareInterface;
use Friend\FriendInterface;
use Friend\FriendTrait;
use User\User;
use User\UserInterface;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class UserEntity
 */
class UserEntity extends User implements
    UserInterface,
    LinkCollectionAwareInterface,
    ScopeAwareInterface,
    FriendInterface
{
    use FriendTrait;

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
        if ($this->type === null && !empty($type)) {
            $this->type = $type;
        }
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
     * Get link collection
     *
     * @return LinkCollection
     */
    public function getLinks()
    {
        if (!$this->links instanceof LinkCollection) {
            $this->setLinks(new LinkCollection());
        }

        $this->injectLinks($this->links);
        return $this->links;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return strtolower($this->getType());
    }

    /**
     * @param LinkCollection $links
     */
    protected function injectLinks(LinkCollection $links)
    {
        if (!$links->has('profile') && !empty($this->getUserId())) {
            $links->add(new ProfileLink($this->getUserId()));
        }

        if (!$links->has('user_image') && !empty($this->getUserId())) {
            $links->add(new UserImageLink($this->getUserId()));
        }

        if (!$links->has('user_flip') && $this->getType() === static::TYPE_CHILD) {
            $links->add(new UserFlipLink($this->getUserId()));
        }
    }
}
