<?php
namespace Api\V1\Rest\Group;

use Api\Links\GroupUserLink;
use Api\ScopeAwareInterface;
use Group\Group;
use Group\GroupInterface;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class GroupEntity
 * @package Api\V1\Rest\Group
 */
class GroupEntity extends Group implements GroupInterface, LinkCollectionAwareInterface, ScopeAwareInterface
{
    /**
     * @var LinkCollection
     */
    protected $links;

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        unset($array['left']);
        unset($array['right']);
        unset($array['depth']);

        $array['links'] = $this->getLinks();
        return $array;
    }

    /**
     * Gets the entity type to allow the rbac to set the correct scope
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'group';
    }

    /**
     * @param LinkCollection $links
     * @return mixed
     */
    public function setLinks(LinkCollection $links)
    {
        $links->add(new GroupUserLink($this));
        $this->links = $links;
    }

    /**
     * @return LinkCollection
     */
    public function getLinks()
    {
        if ($this->links === null) {
            $this->setLinks(new LinkCollection());
        }
        
        return $this->links;
    }
}
