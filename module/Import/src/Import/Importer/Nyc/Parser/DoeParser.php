<?php

namespace Import\Importer\Nyc\Parser;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Parser\Excel\ClassWorksheetParser as ClassParser;
use Import\Importer\Nyc\Parser\Excel\StudentWorksheetParser as StudentParser;
use Import\Importer\Nyc\Parser\Excel\TeacherWorksheetParser as TeacherParser;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use Security\Service\SecurityServiceInterface;
use Zend\Log\Logger;

/**
 * Class DoePreProcessor
 */
class DoeParser extends AbstractParser
{
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
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var string
     */
    protected $teacherCode;

    /**
     * @var string
     */
    protected $studentCode;

    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * DoeParser constructor.
     *
     * @param ClassRoomRegistry $classRegistry
     * @param TeacherRegistry $teacherRegistry
     * @param StudentRegistry $studentRegistry
     * @param UserGroupServiceInterface $userGroupService
     * @param GroupServiceInterface $groupService
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(
        ClassRoomRegistry $classRegistry,
        TeacherRegistry $teacherRegistry,
        StudentRegistry $studentRegistry,
        UserGroupServiceInterface $userGroupService,
        GroupServiceInterface $groupService,
        SecurityServiceInterface $securityService
    ) {
        $this->classRegistry    = $classRegistry;
        $this->teacherRegistry  = $teacherRegistry;
        $this->studentRegistry  = $studentRegistry;
        $this->userGroupService = $userGroupService;
        $this->groupService     = $groupService;
        $this->securityService  = $securityService;
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
     * @param string $teacherCode
     */
    public function setTeacherCode($teacherCode)
    {
        $this->teacherCode = $teacherCode;
    }

    /**
     * @param string $studentCode
     */
    public function setStudentCode($studentCode)
    {
        $this->studentCode = $studentCode;
    }

    /**
     * PreProcess a file
     */
    public function preProcess()
    {
        $this->getLogger()->info('Starting to process file: ' . $this->getFileName());
        if ($this->fileName === null || empty($this->teacherCode) || empty($this->studentCode)) {
            throw new \RuntimeException('Cannot pre process: Missing required fields');
        }

        if ($this->school === null) {
            throw new \RuntimeException('Cannot pre process: No school set');
        }

        $reader      = \PHPExcel_IOFactory::load($this->fileName);
        $foundSheets = [];
        foreach ($reader->getAllSheets() as $sheet) {
            $this->getLogger()->debug('Found Sheet: ' . $sheet->getTitle());
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

        $this->parseFoundSheets($foundSheets);
    }

    /**
     * Parses the sheets that were found
     *
     * @param array $foundSheets
     */
    protected function parseFoundSheets(array $foundSheets)
    {
        foreach ([ClassParser::SHEET_NAME, TeacherParser::SHEET_NAME, StudentParser::SHEET_NAME] as $requiredSheet) {
            $this->getLogger()->debug('Checking for sheet: ' . $requiredSheet);
            if (!isset($foundSheets[$requiredSheet])) {
                $this->addError(
                    sprintf('Required sheet "%s" is missing', $requiredSheet)
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
            $this->addCodeActions();
        }
    }

    protected function addCodeActions()
    {
        foreach ($this->teacherRegistry as $teacher) {
            $this->addAction(new AddCodeToUserAction($teacher, $this->securityService, $this->teacherCode));
        }

        foreach ($this->studentRegistry as $student) {
            $this->addAction(new AddCodeToUserAction($student, $this->securityService, $this->studentCode));
        }
    }

    /**
     * Creates the actions to associate the user to the group
     */
    protected function createAssociationActions()
    {
        $this->getLogger()->info('Creating associations to classes');
        $schoolGroup = new ClassRoom('school', $this->school->getTitle());
        $schoolGroup->setGroup($this->school);
        foreach ($this->teacherRegistry as $teacher) {
            $groupType = "class";
            if (!$teacher->hasClassAssigned()) {
                $teacher->setClassRoom($schoolGroup);
                $groupType = "school";
            }

            $this->getLogger()->debug(sprintf('Adding teacher "%s" to %s', $teacher->getEmail(), $groupType));
            $this->addAction(new AddTeacherToGroupAction($teacher, $this->userGroupService));
        }

        foreach ($this->studentRegistry as $student) {
            $this->getLogger()->debug('Adding student to class');
            $this->addAction(new AddStudentToGroupAction($student, $this->userGroupService));
        }

        foreach ($this->classRegistry as $classRoom) {
            if ($classRoom->isNew()) {
                $this->getLogger()->debug(sprintf('Adding class %s to school', $classRoom->getTitle()));
                $this->addAction(new AddClassToSchooAction($this->school, $classRoom, $this->groupService));
            }
        }
    }

    /**
     * @return ClassParser
     */
    public function getClassParser()
    {
        return $this->classParser;
    }

    /**
     * @return TeacherParser
     */
    public function getTeacherParser()
    {
        return $this->teacherParser;
    }

    /**
     * @return StudentParser
     */
    public function getStudentParser()
    {
        return $this->studentParser;
    }

    /**
     * Parses the class sheet
     *
     * Merges the errors, warnings and actions
     */
    protected function parseClassSheet()
    {
        $this->getLogger()->info('Parsing Class Sheet');
        $this->getClassParser()->preProcess();
    }

    /**
     * Parses the teacher sheet
     *
     * Merges the errors, warnings and actions
     */
    protected function parseTeacherSheet()
    {
        $this->getLogger()->info('Parsing Teacher sheet');
        $this->getTeacherParser()->preProcess();
    }

    /**
     * Parses the student sheet
     *
     * Merges the errors, warnings and actions
     */
    protected function parseStudentSheet()
    {
        $this->getLogger()->info('Parsing Student Sheet');
        $this->getStudentParser()->preProcess();
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
