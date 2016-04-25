<?php

namespace Notice\Listeners;

use AcMailer\Service\MailServiceAwareTrait;
use Forgot\Service\ForgotServiceInterface;
use Notice\EmailModel\ForgotEmailModel;

use Notice\NoticeInterface;
use User\Child;

use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class ForgotPasswordListener
 */
class ForgotPasswordListener implements NoticeInterface
{
    use MailServiceAwareTrait;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $manager)
    {
        $this->listeners[] = $manager->attach(
            ForgotServiceInterface::class,
            'forgot.password.post',
            [$this, 'notify']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(ForgotServiceInterface::class, $listener);
        }
    }

    /**
     * Send out a notice about the import
     *
     * @param Event $event
     * @return null
     */
    public function notify(Event $event)
    {
        $user = $event->getParam('user');

        if (!$user instanceof UserInterface) {
            return null;
        }

        if ($user instanceof Child) {
            return null;
        }

        $this->getMailService()->getMessage()->setTo($user->getEmail());
        $this->getMailService()->getMessage()->setSubject('Reset Password Code');
        $this->getMailService()->setTemplate(
            new ForgotEmailModel($user, $event->getParam('code'))
        );

        $this->getMailService()->send();
        return null;
    }
}
