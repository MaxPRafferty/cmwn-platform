<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use Import\Importer\Nyc\Students\AddStudentAction;
use Import\Importer\Nyc\Students\Student;
use Import\Importer\Nyc\Students\StudentRegistry;
use \PHPExcel_Worksheet_RowCellIterator as CellIterator;
use \PHPExcel_Worksheet as WorkSheet;

/**
 * Class StudentWorksheetParser
 */
class StudentWorksheetParser extends AbstractExcelParser
{
    const SHEET_NAME = "Students";

    /**
     * @var StudentRegistry
     */
    protected $studentRegistry;

    /**
     * @var ClassRoomRegistry
     */
    protected $classRoomRegistry;

    /**
     * StudentWorksheetParser constructor.
     *
     * @param WorkSheet $worksheet
     * @param StudentRegistry $studentRegistry
     * @param ClassRoomRegistry $classRoomRegistry
     * @throws InvalidWorksheetException
     */
    public function __construct(
        WorkSheet $worksheet,
        StudentRegistry $studentRegistry,
        ClassRoomRegistry $classRoomRegistry
    ) {
        if ($worksheet->getTitle() !== static::SHEET_NAME) {
            throw new InvalidWorksheetException(sprintf('Missing worksheet "%s"', static::SHEET_NAME));
        }

        parent::__construct($worksheet);
        $this->studentRegistry   = $studentRegistry;
        $this->classRoomRegistry = $classRoomRegistry;
    }

    /**
     * Returns a list of header fields expected
     *
     * @return mixed
     */
    protected function getHeaderFields()
    {
        return [
            'A' => 'DDBNNN',
            'B' => 'LAST NAME',
            'C' => 'FIRST NAME',
            'D' => 'STUDENT ID',
            'E' => 'SEX',
            'F' => 'BIRTH DT',
            'G' => 'OFF CLS',
            'H' => 'GRD CD',
            'I' => 'GRD LVL',
            'J' => 'STREET NUM',
            'K' => 'STREET',
            'L' => 'APT',
            'M' => 'CITY',
            'N' => 'ST',
            'O' => 'ZIP',
            'P' => 'HOME PHONE',
            'Q' => 'ADULT LAST 1',
            'R' => 'ADULT FIRST 1',
            'S' => 'ADULT PHONE 1',
            'T' => 'ADULT LAST 2',
            'U' => 'ADULT FIRST 2',
            'V' => 'ADULT PHONE 2',
            'W' => 'ADULT LAST 3',
            'X' => 'ADULT FIRST 3',
            'Y' => 'ADULT PHONE 3',
            'Z' => 'STUDENT PHONE',
            'AA' => 'MEAL CDE',
            'AB' => 'YTD ATTD PCT',
            'AC' => 'EMAIL',
        ];
    }

    /**
     * Returns back a list of fields/Cells that are required
     *
     * @return array
     */
    protected function getRequiredFields()
    {
        return [
            'B' => 'LAST NAME',
            'C' => 'FIRST NAME',
            'D' => 'STUDENT ID',
            'F' => 'BIRTH DT',
            'G' => 'OFF CLS',
        ];
    }

    /**
     * PreProcess a file
     */
    public function preProcess()
    {
        $this->getLogger()->info('Pre processing Students worksheet');
        $iterator = $this->getWorksheetIterator();
        $iterator->rewind();

        $this->workSheet;
        /** @var CellIterator $cellIterator */
        $cellIterator = $iterator->current()->getCellIterator();
        if ($this->checkHeader($cellIterator) === false) {
            $this->getLogger()->warn('Unable to continue pre processing when header fields are in correct');
            return;
        }

        $iterator->next();
        while ($iterator->valid()) {
            $row       = $iterator->current();
            $rowNumber = $iterator->key();
            $iterator->next();

            // Skip if empty only if the next row is not empty
            if ($this->isRowEmpty($row, 'A', 'AC') && $iterator->valid()) {
                $this->addWarning(
                    'No data found between cells <b>"A"</b> and <b>"AC"</b> Skipping this row',
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $this->getDdbnnn($cellIterator, $rowNumber);
            $rowData = $this->parseRow($cellIterator, $rowNumber);

            $student = $this->getStudentFromRow($rowData, $rowNumber);

            if ($student && $this->studentRegistry->localExists($student->getStudentId())) {
                $this->addError(
                    sprintf(
                        'A student with the id <b>STUDENT ID - "%s"</b> appears more than once in this sheet',
                        $student->getStudentId()
                    ),
                    static::SHEET_NAME,
                    $rowNumber
                );
            }
            
            if ($this->hasErrors()) {
                continue;
            }

            $this->studentRegistry->addStudent($student);
            $this->getClassForStudent($rowData, $student, $rowNumber);
        };
        
        if (!$this->hasErrors()) {
            $this->createActions();
        }
    }


    /**
     * Creates all the add student actions
     *
     * @todo Add the association action to classes
     * @todo add the association action to the school for students with no classes
     */
    protected function createActions()
    {
        $this->getLogger()->info('Creating actions for students');
        foreach ($this->studentRegistry as $student) {
            if (!$student->isNew()) {
                $this->getLogger()->debug(
                    sprintf('Student with email %s already exists', $student->getEmail())
                );

                continue;
            }

            $this->addAction(new AddStudentAction($this->studentRegistry->getUserService(), $student));
        }
    }
    
    /**
     * @param array $rowData
     * @param $rowNumber
     * @return bool|Student
     */
    protected function getStudentFromRow($rowData, $rowNumber)
    {
        $bdayString = isset($rowData['BIRTH DT']) ? $rowData['BIRTH DT'] : null;

        try {
            $birthday = $bdayString !== null ? new \DateTime($bdayString) : null;
        } catch (\Exception $exception) {
            $birthday = null;
        }

        if ($birthday === null) {
            $this->addError(
                sprintf('Invalid birthday <b>"%s"</b>', $bdayString),
                static::SHEET_NAME,
                $rowNumber
            );
        }

        // check row data here so we can test the birthday
        if ($birthday === null || $rowData === false) {
            return false;
        }

        $student = new Student();
        $student->setFirstName($rowData['FIRST NAME'])
            ->setLastName($rowData['LAST NAME'])
            ->setEmail($rowData['EMAIL'])
            ->setGender($rowData['SEX'])
            ->setStudentId($rowData['STUDENT ID'])
            ->setBirthday($birthday);

        unset($rowData['FIRST NAME']);
        unset($rowData['LAST NAME']);
        unset($rowData['SEX']);
        unset($rowData['GENDER']);
        unset($rowData['BIRTH DT']);
        unset($rowData['EMAIL']);
        unset($rowData['DDBNNN']);
        unset($rowData['STUDENT ID']);
        unset($rowData['OFF CLS']);

        $student->setExtra($rowData);

        return $student;
    }

    /**
     * Attaches the class room to the student
     *
     * @param array $rowData
     * @param Student $student
     * @param $rowNumber
     * @throws \Import\Importer\Nyc\Exception\InvalidStudentException
     */
    protected function getClassForStudent(array $rowData, Student $student, $rowNumber)
    {
        $class   = $rowData['OFF CLS'];
        if (empty($class)) {
            return;
        }

        if (!$this->classRoomRegistry->offsetExists($class)) {
            $this->addError(
                sprintf('Class ID <b>"%s"</b> was not found', $class),
                static::SHEET_NAME,
                $rowNumber
            );
            return;
        }

        $student->setClassRoom($this->classRoomRegistry->offsetGet($class));
    }
}
