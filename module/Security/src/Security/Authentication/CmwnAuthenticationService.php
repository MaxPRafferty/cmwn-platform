<?php

namespace Security\Authentication;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationService as ZfAuthService;
use Zend\Authentication\Storage\StorageInterface;

/**
 * Class CmwnAuthenticationService
 *
 * @package Security\Authentication
 */
class CmwnAuthenticationService extends ZfAuthService implements AuthenticationServiceInterface
{
    public function __construct(StorageInterface $storage, AdapterInterface $adapter)
    {
        $this->setStorage($storage);
        $this->setAdapter($adapter);
    }
}
