<?php

namespace IntegrationTest\Security\Rbac;

/**
 * Class LoggedInChildDataProvider
 *
 * @package SecurityTest\Authorization\Rbac
 */
class LoggedInChildDataProvider extends AbstractRoleDataProvider
{
    /**
     * LoggedInChildDataProvider constructor.
     *
     * @param $rolesConfig
     */
    public function __construct($rolesConfig)
    {
        parent::__construct('logged_in.child', $rolesConfig);
        $this->setAllowed('view.games');
        $this->setAllowed('view.flip');
    }
}
