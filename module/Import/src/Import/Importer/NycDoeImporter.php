<?php

namespace Import\Importer;

use Import\PreprocessInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\Log\Writer\Noop;

/**
 * Class NycDoeImporter
 *
 * @package Import\Importer
 */
class NycDoeImporter implements PreprocessInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const CLASS_SHEET_NAME = 'Classes';

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
            }
        }
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
        return [];
    }

    protected function verifyClassesSheet()
    {
        $sheet = $this->reader->getSheet();
    }

    /**
     * Validates the DDBNNN Format
     *
     * @param $ddbnnn
     * @return bool
     */
    protected function validateDdbnnn($ddbnnn)
    {
        $result = preg_split('/(?<=[0-9])(?=[a-z]+)/i', $ddbnnn);
        $this->getLogger()->debug('Validating DDBNNN: ' . $ddbnnn);
        if (count($result) !== 2) {
            $this->getLogger()->debug('Invalid DDBNNN');
            return false;
        }

        return true;
    }
}
