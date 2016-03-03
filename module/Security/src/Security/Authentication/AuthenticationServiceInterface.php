<?php


namespace Security\Authentication;

use \Zend\Authentication\AuthenticationServiceInterface as ZfAuthServiceInterface;

/**
 * Interface AuthenticationServiceInterface
 *
 * Requires the getAdapter
 *
 * @package Security\Authentication
 */
interface AuthenticationServiceInterface extends ZfAuthServiceInterface
{
    /**
     * @return CmwnAuthenticationAdapter
     */
    public function getAdapter();
}
