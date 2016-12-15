<?php

namespace Import\Importer\Nyc;

use Application\Exception\NotFoundException;
use Application\Utils\Date\DateTimeFactory;
use Application\Utils\NoopLoggerAwareTrait;
use Group\GroupAwareInterface;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Import\Importer\Nyc\Parser\AddCodeToUserAction;
use Import\Importer\Nyc\Parser\DoeParser;
use Import\ImporterInterface;
use Import\ProcessorErrorException;
use Job\Feature\DryRunInterface;
use Job\Feature\DryRunTrait;
use Notice\NotificationAwareInterface;
use Notice\NotificationAwareTrait;
use Org\OrgAwareInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Log\LoggerAwareInterface;

/**
 * Class NycDoeImporter
 *
 * @package Import\Importer
 */
class DoeImporter implements
    LoggerAwareInterface,
    EventManagerAwareInterface,
    ImporterInterface,
    GroupAwareInterface,
    DryRunInterface,
    NotificationAwareInterface
{
    use EventManagerAwareTrait;
    use NoopLoggerAwareTrait;
    use DryRunTrait;
    use NotificationAwareTrait;

    /**
     * @var array Adds the Importer interface the shared manager
     */
    protected $eventIdentifier = ['Import\ImporterInterface'];

    /**
     * @var string the file name to process
     */
    protected $fileName;

    /**
     * @var DoeParser
     */
    protected $parser;

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
     * @var \DateTime
     */
    protected $codeStart;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * DoeImporter constructor.
     *
     * @param DoeParser $parser
     * @param GroupServiceInterface $groupService
     */
    public function __construct(DoeParser $parser, GroupServiceInterface $groupService)
    {
        $this->parser       = $parser;
        $this->groupService = $groupService;
        $this->codeStart    = DateTimeFactory::factory('now');
    }

    /**
     * Adds the school to the parser
     */
    public function attachDefaultListeners()
    {
        $this->getEventManager()->attach('nyc.import.excel', function () {
            $this->getLogger()->debug(sprintf('Attaching school "%s" to parser', $this->getSchool()));
            $this->parser->setSchool($this->getSchool());
        });

        $this->getEventManager()->attach('nyc.import.excel', function () {
            $this->getLogger()->debug('Attaching logger to parser');
            $this->parser->setLogger($this->getLogger());
        });
    }

    /**
     * Sets the group to this object
     *
     * @param GroupInterface|string $group
     */
    public function setGroup($group)
    {
        $this->setSchool($group);
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
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param \DateTime|string|int $startDate
     */
    public function setCodeStart($startDate)
    {
        $this->codeStart = DateTimeFactory::factory($startDate ?? 'now');
    }

    /**
     * @return \DateTime
     */
    public function getCodeStart()
    {
        return $this->codeStart;
    }

    /**
     * @param $school
     * @throws \Exception
     */
    public function setSchool($school)
    {
        if ($school !== null && !$school instanceof GroupInterface) {
            $this->getLogger()->debug('Loading school from database');
            try {
                $school = $this->groupService->fetchGroup($school);
            } catch (\Exception $schoolNotFound) {
                $message = sprintf('Error Fetching school %s: %s', $school, $schoolNotFound->getMessage());
                $this->getLogger()->crit($message);

                throw new \RuntimeException($message, 500, $schoolNotFound);
            }

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
            $fileName = isset($fileName['tmp_name']) ? $fileName['tmp_name'] : null;
        }

        $this->fileName = $fileName;
        $this->parser->setFileName($fileName);
        return $this;
    }

    /**
     * Performs the work for the job
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function perform()
    {
        $this->getLogger()->notice('Importing file: ' . $this->getFileName());

        $event = new Event('nyc.import.excel', $this->parser);
        $this->parser->setStudentCode($this->studentCode);
        $this->parser->setTeacherCode($this->teacherCode);
        $this->parser->setEmail($this->getEmail());

        try {
            if ($this->getEventManager()->trigger($event)->stopped()) {
                $this->getLogger()->notice('Response caused processing to stop');
                return;
            }

            $this->getLogger()->info('Pre-processing');
            $this->parser->preProcess();
            if ($this->parser->hasErrors()) {
                throw new ProcessorErrorException('Parser has errors');
            }

            $this->getLogger()->info('Pre-processing complete');
            $event->setName('nyc.import.excel.run');
            $this->getEventManager()->trigger($event);

            $actions = $this->parser->getActions();
            $actions->top();
            $this->getLogger()->info(sprintf('Got %d actions', count($actions)));
            while ($actions->valid()) {
                $currentAction = $actions->current();
                $actions->next();

                if ($currentAction instanceof OrgAwareInterface) {
                    $currentAction->setOrgId($this->school->getOrganizationId());
                }

                $this->getLogger()->info('Action: ' . $currentAction);

                if ($this->isDryRun()) {
                    $this->getLogger()->debug('Not executing action (dry-run)');
                    continue;
                }

                if (null !== $this->codeStart && $currentAction instanceof AddCodeToUserAction) {
                    $currentAction->setCodeStart($this->codeStart);
                }

                $currentAction->execute();
                $this->getLogger()->debug('Action executed');
            }

            $this->getLogger()->notice('Done Executing Actions');
            $event->setName('nyc.import.excel.complete');
            $this->getEventManager()->trigger($event);
        } catch (ProcessorErrorException $processException) {
            $this->getLogger()->warn('Processor has errors', $this->parser->getErrors());
            $event->setName('nyc.import.excel.error');
            $this->getEventManager()->trigger($event);
            throw $processException;
        } catch (\Throwable $processException) {
            $this->getLogger()->alert($processException->getMessage());
            $event->setName('nyc.upload.excel.failed');
            $this->getEventManager()->trigger($event);
            throw $processException;
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
            'type'         => get_class($this),
            'file'         => $this->getFileName(),
            'teacher_code' => $this->teacherCode,
            'student_code' => $this->studentCode,
            'school'       => $this->school instanceof GroupInterface ? $this->school->getGroupId() : null,
            'email'        => $this->getEmail(),
            'code_start'   => $this->getCodeStart() !== null ? $this->getCodeStart()->format("Y-m-d H:i:s") : null,
        ];
    }

    /**
     * Returns the values back to the object
     *
     * @param array $data
     * @return mixed
     */
    public function exchangeArray(array $data)
    {
        $defaults = [
            'file'         => null,
            'teacher_code' => null,
            'student_code' => null,
            'school'       => null,
            'email'        => null,
            'code_start'   => null,
        ];

        $data = array_merge($defaults, $data);
        $this->setFileName($data['file']);

        $this->teacherCode = $data['teacher_code'];
        $this->studentCode = $data['student_code'];

        $this->setSchool($data['school']);
        $this->setEmail($data['email']);
        $this->setCodeStart($data['code_start']);
    }
}
