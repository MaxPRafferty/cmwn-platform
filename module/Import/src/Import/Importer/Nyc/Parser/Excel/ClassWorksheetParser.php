<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use \PHPExcel_Worksheet_RowCellIterator as CellIterator;
use \PHPExcel_Worksheet as WorkSheet;

/**
 * Class ClassWorksheetParser
 */
class ClassWorksheetParser extends AbstractParser
{
    const SHEET_NAME = 'Classes';

    /**
     * @var ClassRoomRegistry
     */
    protected $classRegistry;

    /**
     * ClassesParser constructor.
     *
     * @param WorkSheet $worksheet
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
     * @return ClassRoomRegistry
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
                    'No data found between cells "A" and "D" Skipping this row',
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

            $classTitle = $this->getClassTitle($cellIterator);

            if (empty($classTitle)) {
                $this->addError(
                    'Missing class title',
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            $classId = $this->getClassId($cellIterator);
            if (empty($classId)) {
                $this->addError(
                    'Missing class id',
                    static::SHEET_NAME,
                    $rowNumber
                );
                continue;
            }

            $subClasses = $this->getSubClasses($cellIterator);
            $this->classRegistry->addClassroom(new ClassRoom($classTitle, $classId, $subClasses));
        };
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
        return empty($classString) ? [] : explode(',', $classString);
    }

    /**
     * Gets the class Id
     *
     * @param CellIterator $cellIterator
     * @return mixed
     * @throws \PHPExcel_Exception
     */
    protected function getClassId(CellIterator $cellIterator)
    {
        return trim($cellIterator->seek('C')->current()->getFormattedValue());
    }

    /**
     * Gets the Title of the class from a row
     *
     * @param CellIterator $cellIterator
     * @return string
     * @throws \PHPExcel_Exception
     */
    protected function getClassTitle(CellIterator $cellIterator)
    {
        return trim($cellIterator->seek('B')->current()->getFormattedValue());
    }

    /**
     * @param CellIterator $cellIterator
     * @return bool
     * @throws \PHPExcel_Exception
     */
    protected function checkHeader(CellIterator $cellIterator)
    {
        $headerOk = true;
        try {
            if ($cellIterator->seek('A')->current()->getFormattedValue() !== 'DDBNNN') {
                $headerOk = false;
                $this->addError('Column "A" in the header is not labeled as "DDBNNN"', static::SHEET_NAME, 1);
            }

            if ($cellIterator->seek('B')->current()->getFormattedValue() !== 'TITLE') {
                $headerOk = false;
                $this->addError('Column "B" in the header is not labeled as "TITLE"', static::SHEET_NAME, 1);
            }

            if ($cellIterator->seek('C')->current()->getFormattedValue() !== 'OFF CLS') {
                $headerOk = false;
                $this->addError('Column "C" in the header is not labeled as "OFF CLS"', static::SHEET_NAME, 1);
            }

            if ($cellIterator->seek('D')->current()->getFormattedValue() !== 'SUB CLASSES') {
                $headerOk = false;
                $this->addError('Column "D" in the header is not labeled as "SUB CLASSES"', static::SHEET_NAME, 1);
            }
        } catch (\PHPExcel_Exception $badHeader) {
            $this->addError(
                'Is missing one or more column(s) between "A" and "D"',
                static::SHEET_NAME,
                1
            );

            $headerOk = false;
        }

        return $headerOk;
    }
}
