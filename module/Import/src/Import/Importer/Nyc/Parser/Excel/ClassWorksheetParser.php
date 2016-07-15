<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\AddClassRoomAction;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use \PHPExcel_Worksheet_RowCellIterator as CellIterator;
use \PHPExcel_Worksheet as WorkSheet;

/**
 * Class ClassWorksheetParser
 */
class ClassWorksheetParser extends AbstractExcelParser
{
    const SHEET_NAME = 'Classes';

    /**
     * @var ClassRoomRegistry
     */
    protected $classRegistry;

    /**
     * ClassWorksheetParser constructor.
     *
     * @param WorkSheet $worksheet
     * @param ClassRoomRegistry $classRoomRegistry
     * @throws InvalidWorksheetException
     */
    public function __construct(WorkSheet $worksheet, ClassRoomRegistry $classRoomRegistry)
    {
        if ($worksheet->getTitle() !== static::SHEET_NAME) {
            throw new InvalidWorksheetException(sprintf('Missing worksheet "%s"', static::SHEET_NAME));
        }

        parent::__construct($worksheet);
        $this->classRegistry = $classRoomRegistry;
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
            'B' => 'TITLE',
            'C' => 'OFF CLS',
            'D' => 'SUB CLASSES',
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
            'A' => 'DDBNNN',
            'B' => 'TITLE',
            'C' => 'OFF CLS',
        ];
    }


    /**
     * @return ClassRoomRegistry
     * @codeCoverageIgnore
     */
    public function getClassRomRegistry()
    {
        return $this->classRegistry;
    }

    /**
     * PreProcess a file
     */
    public function preProcess()
    {
        $this->getLogger()->info('Pre processing Classes worksheet');
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
            if ($this->isRowEmpty($row, 'A', 'C') && $iterator->valid()) {
                $this->addWarning(
                    'No data found between cells <b>"A"</b> and <b>"D"</b> Skipping this row',
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $ddbnnn = $this->getDdbnnn($cellIterator, $rowNumber);
            if ($ddbnnn === false) {
                continue;
            }

            $rowData = $this->parseRow($cellIterator, $rowNumber);
            if ($rowData === false) {
                continue;
            }
            $subClasses = $this->getSubClasses($cellIterator);

            $classRoomId = $rowData['DDBNNN'] . '-' . $rowData['OFF CLS'];
            if (!$this->classRegistry->offsetExists($classRoomId)) {
                $this->classRegistry->addClassroom(
                    new ClassRoom($rowData['TITLE'], $classRoomId, $subClasses)
                );
            }
        };

        $this->checkRegistry();
        if ($this->hasErrors()) {
            return;
        }

        $this->buildActions();
    }

    /**
     * Creates the actions for this worksheet
     *
     */
    protected function buildActions()
    {
        $this->getLogger()->info('Building Actions for classroom');
        foreach ($this->classRegistry as $classRoom) {
            if (!$classRoom->isNew()) {
                $this->getLogger()->debug(sprintf(
                    'Classroom [%s] "%s" is not a new classroom',
                    $classRoom->getClassRoomId(),
                    $classRoom->getTitle()
                ));
                continue;
            }

            $this->getLogger()->debug(sprintf(
                'Creating add action for classroom [%s] "%s"',
                $classRoom->getClassRoomId(),
                $classRoom->getTitle()
            ));

            $this->addAction(new AddClassRoomAction($this->classRegistry->getGroupService(), $classRoom));
        }
    }

    /**
     * Checks the classroom registry for missing classes
     *
     * @return bool
     */
    protected function checkRegistry()
    {
        $this->getLogger()->info('Checking subclasses in the registry');
        foreach ($this->classRegistry as $classRoom) {
            $subClasses = $classRoom->getSubClassRooms();
            if (empty($subClasses)) {
                $this->getLogger()->debug(sprintf(
                    'Class [%s] "%s" has no sub classes',
                    $classRoom->getClassRoomId(),
                    $classRoom->getTitle()
                ));
                continue;
            }

            array_walk($subClasses, [$this, 'checkIfSubClassExists'], [$classRoom]);
        }

        return $this->hasErrors();
    }

    /**
     * Checks if a sub class exists in the registry or not
     *
     * @param $subClassId
     * @param $subClassIndex
     * @param ClassRoom[] $extra
     * @return bool
     */
    public function checkIfSubClassExists($subClassId, $subClassIndex, $extra)
    {
        if ($this->classRegistry->offsetExists($subClassId)) {
            $this->getLogger()->debug('Sub class was found');
            return true;
        }

        $classRoom = $extra[0];

        $this->addError(
            sprintf(
                'A subclass with the id <b>"%s"</b> was not found for Class [<b>%s</b>] "<b>%s</b>"',
                $subClassId,
                $classRoom->getClassRoomId(),
                $classRoom->getTitle()
            ),
            static::SHEET_NAME
        );

        return false;
    }

    /**
     * Gets the sub class Id's
     *
     * @param CellIterator $cellIterator
     * @return mixed
     * @throws \PHPExcel_Exception
     */
    protected function getSubClasses(CellIterator $cellIterator)
    {
        $classString = trim($cellIterator->seek('D')->current()->getFormattedValue());
        $ddbnnn      = trim($cellIterator->seek('A')->current()->getFormattedValue());
        $subClasses  =  empty($classString) ? [] : explode(',', $classString);
        $test = array_map(function ($subClassId) use (&$ddbnnn) {
            return $ddbnnn . '-'. $subClassId;
        }, $subClasses);

        return $test;
    }
}
