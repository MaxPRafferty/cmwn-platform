<?php

namespace Import\Importer\Nyc;

use Import\ParserInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\Log\Writer\Noop;

/**
 * Class NycDoeImporter
 *
 * @package Import\Importer
 */
class DoeImporter 
{
    use LoggerAwareTrait;

    const SHEET_CLASS   = 'Classes';
    const SHEET_STUDENT = 'Students';
    const SHEET_TEACHER = 'Teachers';

    /**
     * @var string the file name to process
     */
    protected $fileName;

    /**
     * @var \PHPExcel
     */
    protected $reader;

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $classWorksheet;

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $teacherWorksheet;

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $studentWorksheet;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * NycDoeImporter constructor.
     */
    public function __construct()
    {
        $this->setLogger(new Logger(['writers' => [new Noop()]]));

    }

    /**
     * Sets the name of the file to import
     *
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->fileName = $file;
        return $this;
    }

    /**
     * Weather this importer can handle the file
     *
     * @return bool
     */
    public function canImport()
    {
        if (null === $this->fileName) {
            $this->getLogger()->err('Importer is missing file');
            return false;
        }

        if (!file_exists($this->fileName)) {
            $this->getLogger()->err(sprintf('File name %s does not exist', $this->fileName));
            return false;
        }

        return true;
    }

    /**
     * Run the importer
     *
     * @return bool
     */
    public function import()
    {
    }

    /**
     * PreProcess a file
     *
     * True - File passes and can be parsed
     * False - File contains user errors and will not be imported
     *
     * @return bool
     */
    public function preProcess()
    {
        $this->getLogger()->info('Starting preprocess for file: ' . $this->fileName);
        $this->reader = \PHPExcel_IOFactory::load($this->fileName);

        $sheets = $this->reader->getAllSheets();

        foreach ($sheets as $sheet) {
            switch ($sheet->getTitle()) {
                case static::SHEET_CLASS:
                    $this->classWorksheet = $sheet;
                    break;

                case static::SHEET_STUDENT:
                    $this->studentWorksheet = $sheet;
                    break;

                case static::SHEET_TEACHER:
                    $this->teacherWorksheet = $sheet;
                    break;
            }
        }

        $this->preProcessSheets();
    }

    /**
     *
     * @return bool
     */
    protected function preProcessSheets()
    {
        return $this->verifyClassesSheet();
    }

    /**
     * Gets a list of errors from the preProcessor
     *
     * If the preProcessor fails, this SHOULD return a list of errors
     * that will be sent back to the user to help them fix
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @throws \PHPExcel_Exception
     */
    protected function verifyClassesSheet()
    {
        if (null === $this->classWorksheet) {
            $this->errors[] = sprintf('The Excel Sheet is missing the workbook called: %s', static::SHEET_CLASS);
            return false;
        }

        $rowIterator = $this->classWorksheet->getRowIterator(1);

        iterator_apply($rowIterator, [$this, 'checkRow'], ['type' => static::SHEET_CLASS]);
    }

    public function checkRow(\PHPExcel_Worksheet_Row $row)
    {
        $cellIterator = $row->getCellIterator('A', 'D');

        foreach ($cellIterator as $cell) {
            /** @var \PHPExcel_Cell $cell */
        }
    }

}
