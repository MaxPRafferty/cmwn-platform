<?php

namespace Notice\Listeners;

use AcMailer\Service\MailServiceAwareTrait;
use Application\Utils\NoopLoggerAwareTrait;
use Import\ImporterInterface;
use Import\ParserInterface;
use Notice\EmailModel\ImportFailedModel;
use Notice\EmailModel\ImportSuccessModel;
use Notice\NoticeInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\View\Exception;

/**
 * Class ImportNotifer
 */
class ImportListener implements NoticeInterface, LoggerAwareInterface
{
    use MailServiceAwareTrait;
    use NoopLoggerAwareTrait;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     * @var ImportSuccessModel
     */
    protected $successModel;

    /**
     * ImportListener constructor.
     * @param ImportSuccessModel $model
     */
    public function __construct(ImportSuccessModel $model)
    {
        $this->successModel = $model;
    }

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
     * @return null
     */
    public function notify(Event $event)
    {
        $parser = $event->getTarget();
        if (!$parser instanceof ParserInterface) {
            return null;
        }

        $this->getLogger()->info('Notfication for event: ' . $event->getName());
        if (strpos($event->getName(), 'error') !== false) {
            $this->getLogger()->info('Notifying Error');
            $this->notifyError($parser);
            return null;
        }

        if (strpos($event->getName(), 'complete') !== false) {
            $this->getLogger()->info('Notifying Success');
            $this->notifySuccess($parser);
            return null;
        }

        if (strpos($event->getName(), 'upload.excel.failed') !== false) {
            $this->getLogger()->info('Notifying Upload Error');
            $this->notifyUploadError($parser);
        }

        return null;
    }

    /**
     * Notifies on import error
     *
     * @param ParserInterface $parser
     * @throws \AcMailer\Exception\MailException
     * @return null
     */
    protected function notifyError(ParserInterface $parser)
    {
        $this->getMailService()->getMessage()->setTo($parser->getEmail());
        $this->getMailService()->getMessage()->setSubject('User import error');
        $this->getMailService()->setTemplate(
            new ImportFailedModel([
                'image_domain' => $this->successModel->getVariable('image_domain'),
                'errors' => $parser->getErrors(),
                'warnings' => $parser->getWarnings()
            ])
        );

        $this->getMailService()->send();
        return null;
    }

    /**
     * Notifies on upload error
     *
     * @param ParserInterface $parser
     * @throws \AcMailer\Exception\MailException
     * @return null
     */
    protected function notifyUploadError(ParserInterface $parser)
    {
        $this->getMailService()->getMessage()->setTo($parser->getEmail());
        $this->getMailService()->getMessage()->setSubject('Upload Failed');
        $this->successModel->setTemplate('email/import/upload.failed.phtml');
        $this->getMailService()->setTemplate($this->successModel);

        $this->getMailService()->send();
        return null;
    }

    /**
     * Notifies on import success
     *
     * @param ParserInterface $parser
     * @throws \AcMailer\Exception\MailException
     * @return null
     */
    protected function notifySuccess(ParserInterface $parser)
    {
        $this->getMailService()->getMessage()->setTo($parser->getEmail());
        $this->getMailService()->getMessage()->setSubject('User import Success');

        $this->successModel->setVariable('warnings', $parser->getWarnings());
        try {
            $this->getMailService()->setTemplate($this->successModel);
        } catch (Exception\RuntimeException $e) {
            return;
        }

        $this->getMailService()->send();
        return null;
    }
}
