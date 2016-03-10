<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use \PHPExcel_Worksheet_RowCellIterator as CellIterator;

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
     * @param \PHPExcel_Worksheet $worksheet
     * @throws InvalidWorksheetException
     */
    public function __construct(\PHPExcel_Worksheet $worksheet)
    {
        if ($worksheet->getTitle() !== static::SHEET_NAME) {
            throw new InvalidWorksheetException();
        }

        parent::__construct($worksheet);
        $this->classRegistry = new ClassRoomRegistry();
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
            if ($this->isRowEmpty($row, 'A', 'D') && $iterator->valid()) {
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

            $classRoom = [
                'title'           => $classTitle,
                'class_id'        => $classId,
                'sub_class_rooms' => $subClasses,
            ];
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
        return explode(',', $classString);
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
        if ($cellIterator->seek('A')->current()->getFormattedValue() !== 'DDBNNN') {
            $headerOk = false;
            $this->addError('Missing "DDBNNN" from header', static::SHEET_NAME, 1);
        }

        if ($cellIterator->seek('B')->current()->getFormattedValue() !== 'TITLE') {
            $headerOk = false;
            $this->addError('Missing "TITLE" from header', static::SHEET_NAME, 1);
        }

        if ($cellIterator->seek('C')->current()->getFormattedValue() !== 'OFF CLS') {
            $headerOk = false;
            $this->addError('Missing "OFF CLS" from header', static::SHEET_NAME, 1);
        }

        if ($cellIterator->seek('D')->current()->getFormattedValue() !== 'SUB CLASSES') {
            $headerOk = false;
            $this->addError('Missing "SUB CLASSES" from header', static::SHEET_NAME, 1);
        }

        return $headerOk;
    }
}
