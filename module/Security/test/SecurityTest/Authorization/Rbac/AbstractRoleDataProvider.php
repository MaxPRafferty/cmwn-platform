<?php

namespace SecurityTest\Authorization\Rbac;

/**
 * Class AbstractRoleDataProvider
 * @package SecurityTest\Authorization\Rbac
 */
abstract class AbstractRoleDataProvider
{
    /**
     * @var string
     */
    protected $roleName;

    /**
     * @var array
     */
    protected $allowed = [];

    /**
     * @var array
     */
    protected $denied = [];

    /**
     * @var array
     */
    protected $allPermissions = [];

    /**
     * AbstractRoleDataProvider constructor.
     * @param $roleName
     * @param $rolesConfig
     */
    public function __construct($roleName, $rolesConfig)
    {
        $this->setRoleName($roleName);

        if (!isset($rolesConfig['permission_labels'])) {
            throw new \InvalidArgumentException("Config missing array key permission_labels");
        }
        $this->allPermissions = array_keys($rolesConfig['permission_labels']);

        $this->denied = $this->allPermissions;
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * @param string $roleName
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

    /**
     * @return array
     */
    public function getAllowed()
    {
        return $this->allowed;
    }

    /**
     * @param $permission
     */
    public function setAllowed($permission)
    {
        if (!in_array($permission, $this->allPermissions)) {
            throw new \InvalidArgumentException("Permission does not exist" . $permission);
        }

        if (in_array($permission, $this->getDenied())) {
            unset($this->denied[array_search($permission, $this->getDenied())]);
        }

        array_push($this->allowed, $permission);
    }

    /**
     * @return array
     */
    public function getDenied()
    {
        return $this->denied;
    }

    /**
     * @param array $permission
     */
    public function setDenied($permission)
    {
        if (!in_array($permission, $this->allPermissions)) {
            throw new \InvalidArgumentException("Permission does not exist" . $permission);
        }

        if (in_array($permission, $this->getAllowed())) {
            unset($this->allowed[array_search($permission, $this->getAllowed())]);
        }

        array_push($this->denied, $permission);
    }
}
