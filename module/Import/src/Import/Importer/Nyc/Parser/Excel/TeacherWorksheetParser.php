<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use Import\Importer\Nyc\Teachers\AddTeacherAction;
use Import\Importer\Nyc\Teachers\Teacher;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use \PHPExcel_Worksheet_RowCellIterator as CellIterator;
use \PHPExcel_Worksheet as WorkSheet;
use Zend\Validator\StaticValidator;

/**
 * Class TeacherWorksheetParser
 */
class TeacherWorksheetParser extends AbstractExcelParser
{
    const SHEET_NAME = "Teachers";

    /**
     * @var TeacherRegistry
     */
    protected $teacherRegistry;

    /**
     * @var ClassRoomRegistry
     */
    protected $classRoomRegistry;

    /**
     * TeacherWorksheetParser constructor.
     *
     * @param WorkSheet $worksheet
     * @param TeacherRegistry $teacherRegistry
     * @param ClassRoomRegistry $classRoomRegistry
     * @throws InvalidWorksheetException
     */
    public function __construct(
        WorkSheet $worksheet,
        TeacherRegistry $teacherRegistry,
        ClassRoomRegistry $classRoomRegistry
    ) {
        if ($worksheet->getTitle() !== static::SHEET_NAME) {
            throw new InvalidWorksheetException(sprintf('Missing worksheet "%s"', static::SHEET_NAME));
        }

        parent::__construct($worksheet);
        $this->teacherRegistry   = $teacherRegistry;
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
            'B' => 'TYPE',
            'C' => 'FIRST NAME',
            'D' => 'MIDDLE NAME',
            'E' => 'LAST NAME',
            'F' => 'EMAIL',
            'G' => 'SEX',
            'H' => 'OFF CLS',
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
            'B' => 'TYPE',
            'C' => 'FIRST NAME',
            'E' => 'LAST NAME',
            'F' => 'EMAIL'
        ];
    }

    /**
     * PreProcess a file
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @todo Break up ?
     */
    public function preProcess()
    {
        $this->getLogger()->info('Pre processing Teachers worksheet');
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
            if ($this->isRowEmpty($row, 'A', 'H') && $iterator->valid()) {
                $this->addWarning(
                    'No data found between cells <b>"A"</b> and <b>"H"</b> Skipping this row',
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $ddbnnn  = $this->getDdbnnn($cellIterator, $rowNumber);
            $rowData = $this->parseRow($cellIterator, $rowNumber);

            if ($rowData === false || $ddbnnn === false) {
                continue;
            }

            if (StaticValidator::execute($rowData['EMAIL'], 'EmailAddress') === false) {
                $this->addError(
                    sprintf('Teacher has invalid email <b>"%s"</b>', $rowData['EMAIL']),
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            $teacher = new Teacher();
            $teacher->setFirstName($rowData['FIRST NAME'])
                ->setMiddleName($rowData['MIDDLE NAME'])
                ->setLastName($rowData['LAST NAME'])
                ->setEmail($rowData['EMAIL'])
                ->setGender($rowData['SEX'])
                ->setRole($rowData['TYPE']);
            $role = $teacher->getRole().'.adult';
            if ($this->getRbac()->getRole($role)->getName() === 'guest') {
                $this->addError(
                    sprintf('Teacher has invalid type <b>"%s"</b>', $rowData['TYPE']),
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            if ($this->teacherRegistry->offsetExists($teacher->getEmail())) {
                $this->getClassForTeacher(
                    $rowData,
                    $this->teacherRegistry->offsetGet($teacher->getEmail()),
                    $rowNumber
                );

                continue;
            }

            $this->teacherRegistry->addTeacher($teacher);
            $this->getClassForTeacher($rowData, $teacher, $rowNumber);
        };

        if (!$this->hasErrors()) {
            $this->createActions();
        }
    }

    /**
     * Creates all the add teacher actions
     */
    protected function createActions()
    {
        foreach ($this->teacherRegistry as $teacher) {
            if (!$teacher->isNew()) {
                $this->getLogger()->debug(
                    sprintf('Teacher with email "%s" found in registry', $teacher->getEmail())
                );

                continue;
            }

            $this->getLogger()->info(sprintf('New teacher: %s', $teacher->getEmail()));
            $this->addAction(new AddTeacherAction($this->teacherRegistry->getUserService(), $teacher));
        }
    }

    /**
     * Attaches the class room to the teacher
     *
     * @param array $rowData
     * @param Teacher $teacher
     * @param $rowNumber
     * @throws \Import\Importer\Nyc\Exception\InvalidTeacherException
     */
    protected function getClassForTeacher(array $rowData, Teacher $teacher, $rowNumber)
    {

        if (empty($rowData['OFF CLS'])) {
            $this->getLogger()->warn(sprintf('Teacher "%s" has empty class', $teacher->getEmail()));
            return;
        }

        $class   = $rowData['OFF CLS'];
        if (!$this->classRoomRegistry->offsetExists($class)) {
            $this->addError(
                sprintf('Class ID <b>"%s"</b> was not found', $class),
                static::SHEET_NAME,
                $rowNumber
            );
            return;
        }

        $this->getLogger()->debug(sprintf(
            'Found class "%s" for teacher "%s"',
            $class,
            $teacher->getEmail()
        ));

        $teacher->setClassRoom($this->classRoomRegistry->offsetGet($class));
    }
}
