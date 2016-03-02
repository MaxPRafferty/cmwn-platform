<?php

namespace Security\Authentication;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\AuthenticationService as ZfAuthService;
use Zend\Authentication\Storage\StorageInterface;

class CmwnAuthenticationService extends ZfAuthService implements AuthenticationServiceInterface
{
    public function __construct(StorageInterface $storage, AdapterInterface $adapter)
    {
        $this->setStorage($storage);
        $this->setAdapter($adapter);
    }
}
