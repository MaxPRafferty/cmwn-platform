<?php

namespace Forgot\Service;

use User\UserInterface;

/**
 * Class ForgotServiceInterface
 */
interface ForgotServiceInterface
{
    /**
     * Generates a code that the user can use to reset their password
     *
     * @param string $email
     * @param null|string $code
     * @return bool|UserInterface
     */
    public function saveForgotPassword($email, $code = null);

    /**
     * @param int $length
     * @return string
     */
    public function generateCode($length = 10);
}
