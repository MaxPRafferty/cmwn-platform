<?php

namespace Notice\Listeners;

use AcMailer\Service\MailServiceAwareTrait;
use Notice\EmailModel\NewUserModel;
use Notice\NoticeInterface;
use User\Child;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class UserEmailListener
 */
class NewUserEmailListener implements NoticeInterface
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
            UserServiceInterface::class,
            'save.new.user.post',
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
            $manager->detach(UserServiceInterface::class, $listener);
        }
    }

    /**
     * Send out a notice about the import
     *
     * @param Event $event
     * @return void
     */
    public function notify(Event $event)
    {
        $user = $event->getParam('user');

        if (!$user instanceof UserInterface) {
            return;
        }

        if ($user instanceof Child) {
            return;
        }

        $this->getMailService()->getMessage()->setTo($user->getEmail());
        $this->getMailService()->getMessage()->setSubject('Welcome to Change my world now');
        $this->getMailService()->setTemplate(new NewUserModel($user));
        $this->getMailService()->send();
    }
}
