<?php
namespace Api\V1\Rest\Group;

use Api\Links\GroupUserLink;
use Api\ScopeAwareInterface;
use Group\Group;
use Group\GroupInterface;
use Org\Organization;
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
     * @var array
     */
    protected $organization;

    /**
     * @var array
     */
    protected $parent;

    /**
     * GroupEntity constructor.
     * @param array $options
     * @param null $organization
     * @param GroupInterface|null $parent
     */
    public function __construct(array $options = [], $organization = null, GroupInterface $parent = null)
    {
        $this->parent       = $parent instanceof GroupInterface ? $parent->getArrayCopy() : null;
        $this->organization = $organization instanceof Organization ? $organization->getArrayCopy() : [];
        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        unset($array['left']);
        unset($array['right']);
        unset($array['depth']);

        $array['links']        = $this->getLinks();
        $array['organization'] = $this->organization;
        $array['parent']       = $this->parent;
        return $array;
    }

    /**
     * Gets the entity type to allow the rbac to set the correct scope
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'group' . (!empty($this->getType()) ? '.' . $this->getType() : '');
    }

    /**
     * @param LinkCollection $links
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
