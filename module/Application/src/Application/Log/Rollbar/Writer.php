<?php

namespace Application\Log\Rollbar;

use Interop\Container\ContainerInterface;
use Security\Authentication\AuthenticationService;
use Security\Exception\ChangePasswordException;
use User\UserInterface;
use Zend\Log\Writer\AbstractWriter;

/**
 * Class Writer
 */
class Writer extends AbstractWriter
{
    /**
     * \RollbarNotifier
     */
    protected $rollbar;

    /**
     * @var ContainerInterface
     */
    protected $services;

    /**
     * Writer constructor.
     *
     * @param \RollbarNotifier $rollbar
     * @param ContainerInterface $services
     * @param null $options
     */
    public function __construct(\RollbarNotifier $rollbar, ContainerInterface $services, $options = null)
    {
        $this->rollbar  = $rollbar;
        $this->services = $services;
        parent::__construct($options);
    }

    /**
     * Write a message to the log.
     *
     * @param  array $event Event data
     *
     * @return void
     */
    protected function doWrite(array $event)
    {
        if (isset($event['timestamp']) && $event['timestamp'] instanceof \DateTime) {
            $event['timestamp'] = $event['timestamp']->format(\DateTime::W3C);
        }

        $this->rollbar->person_fn = [$this, 'getIdentity'];
        $extra                    = array_diff_key($event, ['message' => '', 'priorityName' => '', 'priority' => 0]);
        $exceptionFound           = false;

        array_walk_recursive($extra, function ($item) use (&$exceptionFound) {
            if ($item instanceof \Throwable) {
                $this->rollbar->report_exception($item);

                $exceptionFound = true;
            }
        });

        if (!$exceptionFound) {
            $this->rollbar->report_message($event['message'], $event['priorityName'], $extra);
        }
    }

    /**
     * Flushes to rollbar
     */
    public function shutdown()
    {
        $this->rollbar->flush();
    }

    /**
     * @return AuthenticationService
     */
    protected function getAuthenticationService()
    {
        return $this->services->get(AuthenticationService::class);
    }

    /**
     * Attaches the logged in user information (if available)
     *
     * @return array
     */
    public function getIdentity()
    {
        try {
            if (!$this->getAuthenticationService()->hasIdentity()) {
                return [];
            }

            try {
                $user = $this->getAuthenticationService()->getIdentity();
            } catch (ChangePasswordException $changePassword) {
                $user = $changePassword->getUser();
            }

            if (!$user instanceof UserInterface) {
                return [];
            }

            return [
                'id'       => $user->getUserId(),
                'username' => $user->getUserName(),
                'email'    => $user->getEmail(),
            ];
        } catch (\Exception $authException) {
            return [];
        }
    }
}
