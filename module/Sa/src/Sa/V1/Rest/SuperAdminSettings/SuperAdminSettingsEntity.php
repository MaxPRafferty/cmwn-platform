<?php

namespace Sa\V1\Rest\SuperAdminSettings;

use Api\Links\FlipLink;
use Api\Links\GameDataLink;
use Api\Links\GameLink;
use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\Links\UserLink;
use MenaraSolutions\Geographer\Country;
use MenaraSolutions\Geographer\Earth;
use Zend\Stdlib\ArraySerializableInterface;
use ZF\Hal\Entity;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkCollectionAwareTrait;

/**
 * Class SuperAdminSettingsEntity
 * @package Api\SuperAdminSettings
 */
class SuperAdminSettingsEntity extends Entity implements ArraySerializableInterface
{
    use LinkCollectionAwareTrait;

    /**
     * @var array $roles
     */
    protected $roles;

    /**
     * @var array
     */
    protected $countries;

    /**
     * @return mixed
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * //@ToDo add states for all countries after i18n
     * @param mixed $countries
     */
    public function setCountries(array $countries = null)
    {
        if (empty($countries)) {
            $earthCountries = (new Earth())->getCountries()->toArray();
            $countries = [];
            array_walk($earthCountries, function ($country) use (&$countries) {
                $code = $country['code'];
                $countries[$code] = [];
                $countries[$code]['name'] = $country['name'];
                if ($code === 'US') {
                    $us = Country::build('US');
                    $countries[$code]['states'] = $us->getStates()->toArray();
                }
            });
        }

        $this->countries = $countries;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $config
     */
    public function setRoles(array $config)
    {
        $groupRoles = [];

        if (isset($config['cmwn-roles']) && isset($config['cmwn-roles']['roles'])) {
            $config = $config['cmwn-roles']['roles'];
            foreach ($config as $label => $role) {
                if (isset($role['db-role']) && $role['db-role']) {
                    $groupRoles[] = (explode('.', $label))[0];
                }
            }
        }

        $this->roles['group'] = $groupRoles;
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
            'countries' => $this->getCountries(),
            'roles'     => $this->getRoles(),
        ];
    }

    /**
     * //@ToDo listener should add hal links
     * SuperAdminSettingsEntity constructor.
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->exchangeArray($array);
        $this->setCountries();
        $this->setRoles($array);
        $this->addLink(UserLink::class, 'Manage Users');
        $this->addLink(GameLink::class, 'Manage Games', [null, true]);
        $this->addLink(GameDataLink::class, 'Survey Results', ['all-about-you']);
        $this->addLink(GroupLink::class, 'Manage Groups');
        $this->addLink(OrgLink::class, 'Manage Organizations');
        $this->addLink(FlipLink::class, 'Manage Flips');
        parent::__construct($this);
    }

    /**
     * @param $link
     * @param $label
     * @param array|null ...$options
     */
    protected function addLink($link, $label, array $options = [])
    {
        /**@var Link $link*/
        $link = new $link(...$options);
        $link->setProps(['label' => $label]);
        $this->getLinks()->add($link);
    }
}
