<?php

namespace Spyrit\LightCsv;

use Spyrit\LightCsv\AbstractCsv;

/**
 * Csv Writer
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvWriter extends AbstractCsv
{
    /**
     *
     * @var bool
     */
    protected $useBom = false;

    /**
     * Default Excel Writing configuration
     *
     * @param string $delimiter default = ;
     * @param string $enclosure default = "
     * @param string $encoding  default = CP1252 (date write to the csv will be converted to this encoding from UTF-8)
     * @param string $eol       default = "\r\n"
     * @param string $escape    default = "\\"
     * @param bool   $useBom    default = false (BOM will be writed when opening the file)
     * @param string $translit  default = "translit" (iconv translit option possible values : 'translit', 'ignore', null)
     */
    public function __construct($delimiter = ';', $enclosure = '"', $encoding = 'CP1252', $eol = "\r\n", $escape = "\\", $useBom = false, $translit = 'translit')
    {
        parent::__construct($delimiter, $enclosure, $encoding, $eol, $escape, $useBom, $translit);
        $this->fileHandlerMode = 'wb';
    }

    /**
     * open a csv file to write
     *
     * @param  string                       $filename default = null
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function open($filename = null)
    {
        parent::open($filename);
        $this->writeBom();

        return $this;
    }

    /**
     * get HTTP headers for streaming CSV file
     *
     * @param  string $filename
     * @return array
     */
    public function getHttpHeaders($filename)
    {
        return array(
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment;filename="'.$filename.'"',
        );
    }

    // @codeCoverageIgnoreStart
    /**
     * echo HTTP headers for streaming CSV file
     *
     * @param string $filename
     */
    public function createHttpHeaders($filename)
    {

        $headers = $this->getHttpHeaders($filename);
        foreach ($headers as $key => $value) {
            header($key.': '.$value);
        }
    }
    // @codeCoverageIgnoreEnd

    /**
     *
     * @param resource $fileHandler
     * @param array    $values
     *
     * @return \Spyrit\LightCsv\CsvWriter
     *
     * @throws \InvalidArgumentException
     */
    protected function write($fileHandler, $values)
    {
        $line = implode($this->delimiter, array_map(function($var) {
            // Escape enclosures and enclosed string
            return $this->enclosure.str_replace($this->enclosure, $this->escape.$this->enclosure, $var).$this->enclosure;
        }, $values))
            // Add line ending
            .$this->eol;

        // Write to file
        fwrite($fileHandler, $this->convertEncoding($line, 'UTF-8', $this->encoding));

        return $this;
    }

    /**
     * write a CSV row from a PHP array
     *
     * @param array $values
     *
     * @return \Spyrit\LightCsv\CsvWriter
     */
    public function writeRow(array $values)
    {
        if (!$this->isFileOpened()) {
            $this->openFile($this->fileHandlerMode);
        }

        return $this->write($this->getFileHandler(), $values);
    }

    /**
     * write CSV rows from a PHP arrays
     *
     * @param array rows
     *
     * @return \Spyrit\LightCsv\CsvWriter
     */
    public function writeRows(array $rows)
    {
        foreach ($rows as $values) {
            $this->writeRow($values);
        }

        return $this;
    }
}
