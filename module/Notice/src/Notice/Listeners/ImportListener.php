<?php

namespace Notice\Listeners;

use AcMailer\Service\MailServiceAwareTrait;
use Import\ImporterInterface;
use Import\ParserInterface;
use Notice\EmailModel\ImportFailedModel;
use Notice\EmailModel\ImportSuccessModel;
use Notice\NoticeInterface;
use Notice\NotificationAwareInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class ImportNotifer
 */
class ImportListener implements NoticeInterface
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
            ImporterInterface::class,
            '*',
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
            $manager->detach(ImporterInterface::class, $listener);
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
        $parser = $event->getTarget();
        if (!$parser instanceof ParserInterface) {
            return;
        }

        if (strpos($event->getName(), 'error') !== false) {
            $this->notifyError($parser);
            return;
        }

        if (strpos($event->getName(), 'complete') !== false) {
            $this->notifySuccess($parser);
            return;
        }
    }

    /**
     * Notifies on import error
     *
     * @param ParserInterface $parser
     * @throws \AcMailer\Exception\MailException
     * @return void
     */
    protected function notifyError(ParserInterface $parser)
    {
        if (!$parser instanceof NotificationAwareInterface) {
            return;
        }

        $this->getMailService()->getMessage()->setTo($parser->getEmail());
        $this->getMailService()->getMessage()->setSubject('User import error');
        $this->getMailService()->setTemplate(
            new ImportFailedModel(['errors' => $parser->getErrors(), 'warnings' => $parser->getWarnings()])
        );

        $this->getMailService()->send();
    }

    /**
     * Notifies on import success
     *
     * @param ParserInterface $parser
     * @throws \AcMailer\Exception\MailException
     * @return void
     */
    protected function notifySuccess(ParserInterface $parser)
    {
        if (!$parser instanceof NotificationAwareInterface) {
            return;
        }

        $this->getMailService()->getMessage()->setTo($parser->getEmail());
        $this->getMailService()->getMessage()->setSubject('User import Success');

        $this->getMailService()->setTemplate(
            new ImportSuccessModel(['warnings' => $parser->getWarnings()])
        );

        $this->getMailService()->send();
    }
}
