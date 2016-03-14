<?php

namespace Import\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\Exception\InvalidDdbnnException;
use Import\Importer\Nyc\Parser\AbstractParser;
use PHPExcel_Worksheet_RowCellIterator as CellIterator;
use \PHPExcel_Worksheet_Row as ExcelRow;

/**
 * Base Nyc DOE Excel Parser class
 *
 * Class AbstractParser
 * @package Import\Importer\Nyc\Parser
 */
abstract class AbstractExcelParser extends AbstractParser
{
    const SHEET_NAME = 'NYCDOE';

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $workSheet;

    /**
     * AbstractParser constructor.
     * @param \PHPExcel_Worksheet $worksheet
     */
    public function __construct(\PHPExcel_Worksheet $worksheet)
    {
        $this->workSheet = $worksheet;
    }

    /**
     * @return \PHPExcel_Worksheet_RowIterator
     */
    protected function getWorksheetIterator()
    {
        if ($this->iterator === null) {
            $this->iterator = new \PHPExcel_Worksheet_RowIterator($this->workSheet);
        }

        return $this->iterator;
    }

    /**
     * @param ExcelRow $row
     * @param $start
     * @param $end
     * @return bool
     */
    protected function isRowEmpty(ExcelRow $row, $start, $end)
    {
        $cellIterator = $row->getCellIterator($start, $end);

        foreach ($cellIterator as $cell) {
            /** @var \PHPExcel_Cell $cell */
            if ($cell->getFormattedValue() !== "") {
                return false;
            }
        }

        return true;
    }

    /**
     * Parses the ddbnnn and returns a VO with the parts broken up
     *
     * @param $ddbnnn
     * @return \stdClass
     * @throws InvalidDdbnnException
     */
    public static function parseDdbnnn($ddbnnn)
    {
        preg_match('/^(\d{2})([A-Z])(\d{3})$/', $ddbnnn, $result);
        
        if (count($result) !== 4) {
            throw new InvalidDdbnnException();
        }

        $return = new \stdClass();
        $return->district = $result[1];
        $return->burough  = $result[2];
        $return->class    = $result[3];

        return $return;
    }

    /**
     * Gets the DDBNNN from the row
     *
     * @param CellIterator $cellIterator
     * @param int $rowNumber
     * @return bool|\stdClass
     */
    protected function getDdbnnn(CellIterator $cellIterator, $rowNumber)
    {
        $dString = $cellIterator->seek('A')->current()->getFormattedValue();

        try {
            $this->getLogger()->debug('Validating DDBNNN: ' . $dString);
            $ddbnnn = AbstractExcelParser::parseDdbnnn($dString);
        } catch (InvalidDdbnnException $badNumber) {
            $this->addError(
                sprintf('Invalid DDBNNN "%s"', $dString),
                static::SHEET_NAME,
                $rowNumber
            );

            return false;
        }

        return $ddbnnn;
    }

    /**
     * Parses data out of the row
     *
     * @param CellIterator $row
     * @param $rowNumber
     * @return string[]|bool
     */
    protected function parseRow(CellIterator $row, $rowNumber)
    {
        $rowOk   = true;
        $rowData = [];
        foreach ($this->getHeaderFields() as $cell => $field) {
            $cellValue = $this->getField($row, $cell);

            if (array_key_exists($cell, $this->getRequiredFields()) && empty($cellValue)) {
                $this->addError(
                    sprintf('Missing "%s"', $field),
                    static::SHEET_NAME,
                    $rowNumber
                );
                $rowOk = false;
            }

            $rowData[$field] = $cellValue;
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
        $col = !array_key_exists($col, $this->getHeaderFields())
            ? array_search($col, $this->getHeaderFields())
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
            foreach ($this->getHeaderFields() as $colField => $title) {
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
    
    /**
     * Returns a list of header fields expected
     *
     * @return mixed
     */
    abstract protected function getHeaderFields();

    /**
     * Returns back a list of fields/Cells that are required
     *
     * @return array
     */
    abstract protected function getRequiredFields();
}
