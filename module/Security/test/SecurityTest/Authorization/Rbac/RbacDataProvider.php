<?php

namespace SecurityTest\Authorization\Rbac;

use Application\Exception\NotFoundException;
use ArrayIterator;
use Zend\Filter\StaticFilter;

/**
 * Class RbacDataProvider
 * @package SecurityTest\Authorization\Rbac
 */
class RbacDataProvider implements \IteratorAggregate
{
    /**
     * @var ArrayIterator $iterator
     */
    protected $iterator;

    /**
     * @var array
     */
    protected $roleNames;

    /**
     * @var array
     */
    protected $config;

    /**
     * RbacDataProvider constructor.
     * @param $roleNames
     * @param $config
     */
    public function __construct($roleNames, $config)
    {
        $this->config = $config;
        $this->roleNames = $roleNames;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        $this->buildIterator();
        return $this->iterator;
    }

    public function buildIterator()
    {
        if ($this->iterator!==null) {
            return;
        }

        $this->iterator = new ArrayIterator();

        foreach ($this->roleNames as $roleName) {
            $className = __NAMESPACE__ . '\\' . ucfirst(
                StaticFilter::execute(str_replace('.', '_', $roleName), 'Word\UnderscoreToCamelCase')
            ) . 'DataProvider';

            if (!class_exists($className)) {
                throw new NotFoundException("Missing roles from provider for role $roleName");
            }

            /** @var AbstractRoleDataProvider $roleDataProvider */
            $roleDataProvider = new $className($this->config);
            $this->iterator->append([
                'role' => $roleDataProvider->getRoleName(),
                'allowed' => $roleDataProvider->getAllowed(),
                'denied' => $roleDataProvider->getDenied()
            ]);
        }
    }
}
