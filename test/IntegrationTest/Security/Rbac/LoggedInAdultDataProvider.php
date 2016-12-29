<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class LoggedInAdultDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class LoggedInAdultDataProvider extends AbstractRoleDataProvider
{
    /**
     * LoggedInAdultDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('logged_in.adult', $rolesConfig);
        $this->setAllowed('view.games');
        $this->setAllowed('view.flip');
    }
}
