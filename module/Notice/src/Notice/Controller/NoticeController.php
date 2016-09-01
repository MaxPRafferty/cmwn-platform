<?php

namespace Notice\Controller;

use AcMailer\Service\MailService;
use AcMailer\Service\MailServiceAwareInterface;
use AcMailer\Service\MailServiceAwareTrait;
use Application\Utils\NoopLoggerAwareTrait;
use Notice\EmailModel\ForgotEmailModel;
use Notice\EmailModel\ImportFailedModel;
use Notice\EmailModel\ImportSuccessModel;
use Notice\EmailModel\NewUserModel;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Filter\Priority;
use Zend\Log\Formatter\Simple;
use Zend\Log\Writer\Stream;

/**
 * Class NoticeController
 * @package Notice\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NoticeController extends AbstractConsoleController implements LoggerAwareInterface, MailServiceAwareInterface
{
    use MailServiceAwareTrait;
    use NoopLoggerAwareTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * NoticeController constructor.
     * @param ServiceLocatorInterface $service
     */
    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * @inheritdoc
     */
    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();

        $writer = new Stream(STDOUT);
        $writer->setFormatter(new Simple('%priorityName%: %message%'));

        $priority = Logger::NOTICE;
        $verbose  = $routeMatch->getParam('verbose') || $routeMatch->getParam('v');
        $debug    = $routeMatch->getParam('debug') || $routeMatch->getParam('d');

        $priority = $verbose ? Logger::INFO : $priority;
        $priority = $debug ? Logger::DEBUG : $priority;
        $writer->addFilter(new Priority(['priority' => $priority]));
        $this->getLogger()->addWriter($writer);

        parent::onDispatch($event);
    }

    public function sendMailAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException('Invalid Request');
            }

            $this->getLogger()->notice('Notice controller running');
            $this->getLogger()->info('Turning on verbose');
            $this->getLogger()->debug('Turning on Debug');

            $email = $request->getParam('email');
            $template = $request->getParam('template');

            $mailService = $this->getMailService();
            if ($mailService === null) {
                $mailService = $this->service->get(MailService::class);
                $this->setMailService($mailService);
            }
            $this->getMailService()->getMessage()->setTo($email);
            $config = $this->service->get('config');
            switch ($template) {
                case 'import_success':
                    $this->getMailService()->getMessage()->setSubject('User import Success');
                    $this
                        ->getMailService()
                        ->setTemplate(
                            new ImportSuccessModel([
                                'image_domain' => $config['options']['image_domain'],
                                'warnings' => ['warning1', 'warning2']
                            ])
                        );
                    break;
                case 'import_failure':
                    $this->getMailService()->getMessage()->setSubject('Import Failure');
                    $this->getMailService()
                        ->setTemplate(new ImportFailedModel([
                            'image_domain' => $config['options']['image_domain'],
                            'errors' => ['error1', 'error2'],
                            'warnings' => ['warning1', 'warning2']
                        ]));
                    break;
                case 'forgot_password':
                    $this->getMailService()->getMessage()->setSubject('Forgot Password');
                    $this->getMailService()
                        ->setTemplate(
                            new ForgotEmailModel([
                                'image_domain' => $config['options']['image_domain'],
                                'user' => ['user_id' => 'foo', 'first_name'=>'Foo', 'email' => 'joni@ginasink.com'],
                                'code' => 'foo',
                            ])
                        );
                    break;
                case 'new_user':
                    $this->getMailService()->getMessage()->setSubject('new user');
                    $this->getLogger()->notice("in new user");
                    $this->getMailService()
                        ->setTemplate(
                            new NewUserModel([
                                'image_domain' => $config['options']['image_domain'],
                                'user' => ['user_id' => 'foo', 'email' => 'joni@ginasink.com', 'type' => 'Adult'],
                            ])
                        );
                    break;
            }
            $this->getLogger()->notice("Sending email");
            $this->getMailService()->send();

        } catch (\Exception $processException) {
            $this->getLogger()->emerg(
                sprintf('Error when trying to process: %s', $processException->getMessage()),
                $processException->getTrace()
            );
        }
    }
}
