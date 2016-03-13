<?php

namespace Import\Importer\Nyc\Parser;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Parser\Excel\ClassWorksheetExcelParser as ClassParser;
use Import\Importer\Nyc\Parser\Excel\StudentWorksheetExcelParser as StudentParser;
use Import\Importer\Nyc\Parser\Excel\TeacherWorksheetExcelParser as TeacherParser;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use User\Service\UserServiceInterface;
use Zend\Log\Logger;

/**
 * Class DoePreProcessor
 */
class DoeParser extends AbstractParser
{
    /**
     * @var ClassParser
     */
    protected $classProcessor;

    /**
     * @var TeacherParser
     */
    protected $teacherProcessor;

    /**
     * @var StudentParser
     */
    protected $studentProcessor;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var string name of the file to process
     */
    protected $fileName;

    /**
     * @var ClassRoomRegistry
     */
    protected $classRegistry;

    /**
     * @var TeacherRegistry
     */
    protected $teacherRegistry;

    /**
     * @var StudentRegistry
     */
    protected $studentRegistry;

    /**
     * @var ClassParser
     */
    protected $classParser;

    /**
     * @var TeacherParser
     */
    protected $teacherParser;

    /**
     * @var StudentParser;
     */
    protected $studentParser;

    /**
     * @var GroupInterface; The school this parser is for
     */
    protected $school;

    /**
     * DoeProcessor constructor.
     *
     * @param UserServiceInterface $userService
     * @param GroupServiceInterface $groupService
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(
        UserServiceInterface $userService,
        GroupServiceInterface $groupService,
        UserGroupServiceInterface $userGroupService
    ) {
        $this->userService      = $userService;
        $this->groupService     = $groupService;
        $this->userGroupService = $userGroupService;

        $this->classRegistry    = new ClassRoomRegistry($this->groupService);
        $this->teacherRegistry  = new TeacherRegistry($this->userService);
        $this->studentRegistry  = new StudentRegistry($this->userService);
        $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
    }

    /**
     * Sets the school this parser is for
     *
     * @param GroupInterface $school
     */
    public function setSchool(GroupInterface $school)
    {
        $this->school = $school;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * PreProcess a file
     */
    public function preProcess()
    {
        $this->getLogger()->info('Starting to process file: ' . $this->getFileName());
        if ($this->fileName === null) {
            throw new \RuntimeException('Cannot pre process: No File name set');
        }

        $reader      = \PHPExcel_IOFactory::load($this->fileName);
        $foundSheets = [];
        foreach ($reader->getAllSheets() as $sheet) {
            $this->getLogger()->debug('Found Sheet: ' . $sheet->getTitle());
            if (isset($foundSheets[$sheet->getTitle()])) {
                $this->addError(
                    sprintf('More than one sheet with the name %s found', $sheet->getTitle())
                );

                continue;
            }

            $foundSheets[$sheet->getTitle()] = true;
            switch ($sheet->getTitle()) {
                case ClassParser::SHEET_NAME:
                    $this->buildClassSheet($sheet);
                    break;

                case TeacherParser::SHEET_NAME:
                    $this->buildTeacherSheet($sheet);
                    break;

                case StudentParser::SHEET_NAME:
                    $this->buildStudentSheet($sheet);
                    break;

                default:
                    $this->addWarning(
                        sprintf(
                            'Sheet with the name "%s" was found and will be ignored',
                            $sheet->getTitle()
                        )
                    );
            }
        }

        foreach ([ClassParser::SHEET_NAME, TeacherParser::SHEET_NAME, StudentParser::SHEET_NAME] as $requiredSheet) {
            $this->getLogger()->debug('Checking for sheet: ' . $requiredSheet);
            if (!isset($foundSheets[$requiredSheet])) {
                $this->addError(
                    sprintf('Required sheet "%s" is missing')
                );
            }

            $this->getLogger()->debug('Sheet found');
        }

        if ($this->hasErrors()) {
            $this->getLogger()->notice('Parsing failed, initial checks produced errors');
            return;
        }

        $this->parseClassSheet();
        $this->parseTeacherSheet();
        $this->parseStudentSheet();

        if (!$this->hasErrors()) {
            $this->createAssociationActions();
        }
    }

    protected function createAssociationActions()
    {
        $this->getLogger()->info('Creating associations to classes');

    }

    /**
     * Parses the class sheet
     *
     * Merges the errors, warnings and actions
     */
    protected function parseClassSheet()
    {
        $this->getLogger()->info('Parsing Class Sheet');
        $this->classParser->preProcess();
        if ($this->classParser->hasWarnings()) {
            $this->warnings += $this->classParser->getWarnings();
        }

        if ($this->classParser->hasErrors()) {
            $this->errors += $this->classParser->getErrors();
        }

        if (!$this->hasErrors()) {
            $this->getLogger()->debug('Merging actions from class parser');
            array_walk($this->classParser->getActions(), [$this, 'addAction']);
        }
    }

    /**
     * Parses the teacher sheet
     *
     * Merges the errors, warnings and actions
     */
    protected function parseTeacherSheet()
    {
        $this->teacherParser->preProcess();
        if ($this->teacherParser->hasWarnings()) {
            $this->warnings += $this->teacherParser->getWarnings();
        }

        if ($this->teacherParser->hasErrors()) {
            $this->errors += $this->teacherParser->getErrors();
        }

        if (!$this->hasErrors()) {
            $this->getLogger()->debug('Merging actions from teacher parser');
            array_walk($this->teacherParser->getActions(), [$this, 'addAction']);
        }
    }

    /**
     * Parses the student sheet
     *
     * Merges the errors, warnings and actions
     */
    protected function parseStudentSheet()
    {
        $this->studentParser->preProcess();
        if ($this->studentParser->hasWarnings()) {
            $this->warnings += $this->studentParser->getWarnings();
        }

        if ($this->studentParser->hasErrors()) {
            $this->errors += $this->studentParser->getErrors();
        }

        if (!$this->hasErrors()) {
            $this->getLogger()->debug('Merging actions from student parser');
            array_walk($this->studentParser->getActions(), [$this, 'addAction']);
        }
    }

    /**
     * @param \PHPExcel_Worksheet $worksheet
     */
    protected function buildClassSheet(\PHPExcel_Worksheet $worksheet)
    {
        $this->classParser = new ClassParser($worksheet, $this->classRegistry);
    }

    /**
     * @param \PHPExcel_Worksheet $worksheet
     */
    protected function buildTeacherSheet(\PHPExcel_Worksheet $worksheet)
    {
        $this->teacherParser = new TeacherParser($worksheet, $this->teacherRegistry, $this->classRegistry);
    }

    /**
     * @param \PHPExcel_Worksheet $worksheet
     */
    protected function buildStudentSheet(\PHPExcel_Worksheet $worksheet)
    {
        $this->studentParser = new StudentParser($worksheet, $this->studentRegistry, $this->classRegistry);
    }
}
