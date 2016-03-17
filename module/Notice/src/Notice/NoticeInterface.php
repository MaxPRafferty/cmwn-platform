<?php

namespace Notice;

use AcMailer\Service\MailServiceAwareInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Interface NoticeInterface
 *
 * ${CARET}
 */
interface NoticeInterface extends MailServiceAwareInterface
{
    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $manager);

    /**
     * @param SharedEventManagerInterface $manager
     * @codeCoverageIgnore
     */
    public function detachShared(SharedEventManagerInterface $manager);

    /**
     * Send out a notice about the import
     *
     * @param Event $event
     * @return void
     */
    public function notify(Event $event);
}
