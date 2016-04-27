<?php

namespace Forgot\Service;

use Application\Utils\NoopLoggerAwareTrait;
use Security\Service\SecurityServiceInterface;
use User\UserInterface;
use Zend\Log\LoggerAwareInterface;

/**
 * Class ForgotService
 */
class ForgotService implements LoggerAwareInterface, ForgotServiceInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * ForgotService constructor.
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(SecurityServiceInterface $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Generates a code that the user can use to reset their password
     *
     * @param $email
     * @param null|string $code
     * @return bool|UserInterface
     */
    public function saveForgotPassword($email, $code = null)
    {
        $user = $this->securityService->fetchUserByEmail($email);
        $code = $code === null ? $this->generateCode() : $code;
        $this->securityService->saveCodeToUser($code, $user);
        return $user;
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateCode($length = 10)
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
