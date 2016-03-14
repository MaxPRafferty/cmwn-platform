<?php

namespace Import\Importer\Nyc;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Import\Importer\Nyc\Parser\DoeParser;
use Import\ImporterInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface;
use Zend\Log\LoggerAwareInterface;

/**
 * Class NycDoeImporter
 *
 * @package Import\Importer
 */
class DoeImporter implements LoggerAwareInterface, EventManagerAwareInterface, ImporterInterface
{
    use EventManagerAwareTrait;

    /**
     * @var string the file name to process
     */
    protected $fileName;

    /**
     * @var DoeParser
     */
    protected $parser;

    /**
     * @var LoggerInterface;
     */
    protected $logger;

    /**
     * @var string
     */
    protected $teacherCode;

    /**
     * @var string
     */
    protected $studentCode;

    /**
     * @var GroupInterface
     */
    protected $school;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * DoeImporter constructor.
     * @param DoeParser $parser
     */
    public function __construct(DoeParser $parser, GroupServiceInterface $groupService)
    {
        $this->parser       = $parser;
        $this->groupService = $groupService;
    }

    /**
     * Adds the school to the parser
     */
    public function attachDefaultListeners()
    {
        $this->getEventManager()->attach('nyc.import.excel', function () {
            $this->getLogger()->debug('Attaching school to parser');
            $this->parser->setSchool($this->getSchool());
        });
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->parser->setLogger($logger);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
        }

        return $this->logger;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return GroupInterface
     */
    protected function getSchool()
    {
        return $this->school;
    }

    /**
     * @param $school
     */
    protected function setSchool($school)
    {
        if ($school !== null && !$school instanceof GroupInterface) {
            $this->getLogger()->debug('Loading school from database');
            $school = $this->groupService->fetchGroup($school);
            $this->getLogger()->debug('School loaded');
        }

        $this->school = $school;
    }

    /**
     * @param string $fileName
     * @return DoeImporter
     */
    public function setFileName($fileName)
    {
        // pass through from file upload
        if (is_array($fileName)) {
            $fileName = isset($data['file']['tmp_name']) ? $data['file']['tmp_name'] : null;
        }

        $this->fileName = $fileName;
        $this->parser->setFileName($fileName);
        return $this;
    }

    /**
     * Performs the work for the job
     */
    public function perform()
    {
        $this->getLogger()->notice('Importing file: ' . $this->getFileName());

        $event = new Event('nyc.import.excel', $this->parser);

        try {
            if ($this->getEventManager()->trigger($event)->stopped()) {
                $this->getLogger()->notice('Response caused processing to stop');
                return;
            }

            $this->getLogger()->info('Pre-processing');
            $this->parser->setFileName($this->fileName);
            $this->parser->preProcess();
            if ($this->parser->hasErrors()) {
                throw new \Exception('Parser has errors');
            }

            $this->getLogger()->info('Pre-processing complete');
            $event->setName('nyc.import.excel.run');
            $this->getEventManager()->trigger($event);

            $actions = $this->parser->getActions();
            $actions->top();
            $this->getLogger()->info(sprintf('Got %d actions', count($actions)));
            while ($actions->valid()) {
                $actions->current()->execute();
                $actions->next();
            }

            $event->setName('nyc.import.excel.complete');
            $this->getEventManager()->trigger($event);
        } catch (\Exception $processException) {
            $this->getLogger()->crit('Exception: ' . $processException->getMessage());
            $this->getLogger()->alert('Processor has errors', $this->parser->getErrors());
            $event->setName('nyc.import.excel.error');
            $this->getEventManager()->trigger($event);
            return;
        }
    }

    /**
     * Gets the data that will be passed for the job
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'file'         => $this->getFileName(),
            'teacher_code' => $this->teacherCode,
            'student_code' => $this->studentCode,
            'group'        => $this->school instanceof GroupInterface ? $this->school->getGroupId() : null
        ];
    }

    /**
     * Returns the argumet values back to the object
     *
     * @param array $data
     * @return mixed
     */
    public function exchangeArray(array $data)
    {
        $fileName = isset($data['file']) ? $data['file'] : null;
        $this->setFileName($fileName);
        $this->teacherCode = isset($data['teacher_code']) ? $data['teacher_code'] : null;
        $this->studentCode = isset($data['student_code']) ? $data['student_code'] : null;
        $this->setSchool(isset($data['group']) ? $data['group'] : null);
    }
}
