<?php

namespace Security\Controller;

use Security\PasswordValidator;
use Security\Service\SecurityServiceInterface;
use User\Adult;
use User\Service\UserServiceInterface;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Password;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

/**
 * Class UserController
 */
class UserController extends AbstractConsoleController
{
    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * UserController constructor.
     *
     * @param SecurityServiceInterface $service
     * @param UserServiceInterface $userService
     */
    public function __construct(
        SecurityServiceInterface $service,
        UserServiceInterface $userService
    ) {
        $this->securityService = $service;
        $this->userService     = $userService;
    }

    /**
     * Creates a new super user
     */
    public function createUserAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Bad Request', 400);
        }

        $user = new Adult();

        $user->setFirstName($this->getFirstName());
        $user->setLastName($this->getLastName());
        $user->setEmail($this->getEmail());
        $user->setUserName($this->getUserName());

        $password = $this->getPassword();

        $this->userService->createUser($user);
        $this->getConsole()->writeLine('User Created');

        $this->securityService->savePasswordToUser($user, $password);
        $this->getConsole()->writeLine('Password Saved');

        $this->securityService->setSuper($user, true);
        $this->getConsole()->writeLine('User marked as super user');
    }

    /**
     * Asks for the email
     *
     * @return string
     */
    protected function getEmail()
    {
        $validator = new EmailAddress();
        do {
            $email = Line::prompt('Email: ');
        } while (!$validator->isValid($email));

        return $email;
    }

    /**
     * Asks for the user name
     *
     * @return string
     */
    protected function getUserName()
    {
        $validator = new StringLength(['min' => 3, 'max' => 255]);
        do {
            $username = Line::prompt('Username: ');
        } while (!$validator->isValid($username));

        return $username;
    }

    /**
     * Asks for the user name
     *
     * @return string
     */
    protected function getFirstName()
    {
        $validator = new StringLength(['max' => 255]);
        do {
            $username = Line::prompt('First Name: ');
        } while (!$validator->isValid($username));

        return $username;
    }

    /**
     * Asks for the user name
     *
     * @return string
     */
    protected function getLastName()
    {
        $validator = new StringLength(['max' => 255]);
        do {
            $username = Line::prompt('Last Name: ');
        } while (!$validator->isValid($username));

        return $username;
    }

    /**
     * Asks for the password
     *
     * @return string
     */
    protected function getPassword()
    {
        $validator = new PasswordValidator();
        while (true) {
            $password = Password::prompt('Enter Password: ');
            $confirm  = Password::prompt('Confirm Password: ');

            if (($password !== $confirm)) {
                $this->getConsole()->writeLine('Passwords do not match');
                continue;
            }

            if (!$validator->isValid($password)) {
                $this->getConsole()->writeLine('Invalid Password');
                continue;
            }

            return $password;
        }

        return null;
    }
}
