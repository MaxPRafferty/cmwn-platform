<?php

namespace IntegrationTest;

use Zend\EventManager\EventManagerInterface;
use Zend\Session\AbstractManager;
use Zend\Session\Config\StandardConfig;
use Zend\Session\Storage\ArrayStorage;

/**
 * Class SessionManager
 *
 * @link https://github.com/zendframework/zend-session/blob/master/test/TestAsset/TestManager.php
 */
class SessionManager extends AbstractManager
{
    public $started = false;

    protected $configDefaultClass = StandardConfig::class;
    protected $storageDefaultClass = ArrayStorage::class;

    public function start()
    {
        $this->started = true;
    }

    public function destroy()
    {
        $this->started = false;
    }

    public function stop()
    {
    }

    public function writeClose()
    {
        $this->started = false;
    }

    public function getName()
    {
    }

    public function setName($name)
    {
    }

    public function getId()
    {
    }

    public function setId($id)
    {
    }

    public function regenerateId()
    {
    }

    public function rememberMe($ttl = null)
    {
    }

    public function forgetMe()
    {
    }

    public function setValidatorChain(EventManagerInterface $chain)
    {
    }

    public function getValidatorChain()
    {
    }

    public function isValid()
    {
    }

    public function sessionExists()
    {
    }

    public function expireSessionCookie()
    {
    }
}