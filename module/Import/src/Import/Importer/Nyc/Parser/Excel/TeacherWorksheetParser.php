<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\Exception\InvalidWorksheetException;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use \PHPExcel_Worksheet_RowCellIterator as CellIterator;
use \PHPExcel_Worksheet as WorkSheet;

/**
 * Class TeacherWorksheetParser
 */
class TeacherWorksheetParser extends AbstractParser
{
    const SHEET_NAME = "Teachers";

    /**
     * @var TeacherRegistry
     */
    protected $teacherRegistry;

    protected $headerFields = [
        'A' => 'DDBNNN',
        'B' => 'TYPE',
        'C' => 'FIRST NAME',
        'D' => 'MIDDLE NAME',
        'E' => 'LAST NAME',
        'F' => 'EMAIL',
        'G' => 'SEX',
        'H' => 'OFF CLS',
    ];

    protected $requiredFields = [
        'B' => 'TYPE',
        'C' => 'FIRST NAME',
        'E' => 'LAST NAME',
        'F' => 'EMAIL',
        'H' => 'OFF CLS',
    ];

    /**
     * TeacherWorksheetParser constructor.
     *
     * @param WorkSheet $worksheet
     * @param TeacherRegistry $teacherRegistry
     * @throws InvalidWorksheetException
     */
    public function __construct(WorkSheet $worksheet, TeacherRegistry $teacherRegistry)
    {
        if ($worksheet->getTitle() !== static::SHEET_NAME) {
            throw new InvalidWorksheetException(sprintf('Missing worksheet "%s"', static::SHEET_NAME));
        }

        parent::__construct($worksheet);
        $this->teacherRegistry = $teacherRegistry;
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
            if ($this->isRowEmpty($row, 'A', 'H') && $iterator->valid()) {
                $this->addWarning(
                    'No data found between cells "A" and "H" Skipping this row',
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
        };
    }

    /**
     * Parses data out of the row
     *
     * @param CellIterator $row
     * @param $rowNumber
     * @return array|bool
     */
    protected function parseRow(CellIterator $row, $rowNumber)
    {
        $rowOk   = true;
        $rowData = [];
        foreach ($this->headerFields as $cell => $field) {
            $rowData['field'] = $this->getField($row, $cell);

            if (array_key_exists($cell, $this->requiredFields) && empty($classTitle)) {
                $this->addError(
                    sprintf('Missing %s', $field),
                    static::SHEET_NAME,
                    $rowNumber
                );
                $rowOk = false;
            }
        }

        return $rowOk ? $rowData : false;
    }

    /**
     * Gets a value from the current row
     *
     * @param CellIterator $cellIterator
     * @param $col
     * @return mixed
     * @throws \PHPExcel_Exception
     */
    protected function getField(CellIterator $cellIterator, $col)
    {
        $col = !array_key_exists($col, $this->headerFields)
            ? array_search($col, $this->headerFields)
            : $col;

        return trim($cellIterator->seek($col)->current()->getFormattedValue());
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
            foreach ($this->headerFields as $colField => $title) {
                if ($cellIterator->seek($colField)->current()->getFormattedValue() !== $title) {
                    $headerOk = false;
                    $this->addError(
                        sprintf('Column "%s" in the header is not labeled as "%s"', $colField, $title),
                        static::SHEET_NAME,
                        1
                    );
                }
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
