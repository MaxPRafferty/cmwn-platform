<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use Api\Links\GameDataLink;
use Api\Links\GameLink;
use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\Links\UserLink;
use Zend\Stdlib\ArraySerializableInterface;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkCollectionAwareInterface;
use ZF\Hal\Link\LinkCollectionAwareTrait;

/**
 * Class SuperAdminSettingsEntity
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsEntity implements ArraySerializableInterface, LinkCollectionAwareInterface
{
    use LinkCollectionAwareTrait;

    /**
     * @var array $roles
     */
    protected $roles;

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        $this->setRoles($array['roles'] ?? []);
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'roles' => $this->getRoles(),
            'links' => $this->getLinks(),
        ];
    }

    /**
     * SuperAdminSettingsEntity constructor.
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->exchangeArray($array);
        $this->setRoles($array['roles'] ?? ['group' => ['admin', 'asst_principal', 'principal', 'student', 'teacher']]);
        $this->addLink(UserLink::class, 'Manage Users');
        $this->addLink(GameLink::class, 'Manage Games');
        $this->addLink(GameDataLink::class, 'Survey Results', 'all-about-you');
        $this->addLink(GroupLink::class, 'Manage Groups');
        $this->addLink(OrgLink::class, 'Manage Organizations');
    }

    /**
     * @param $link
     * @param $label
     * @param null $options
     */
    protected function addLink($link, $label, $options = null)
    {
        /**@var Link $link*/
        $link = new $link($options);
        $link->setProps(['label' => $label]);
        $this->getLinks()->add($link);
    }
}
