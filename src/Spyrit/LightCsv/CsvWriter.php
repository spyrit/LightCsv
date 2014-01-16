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
     * Default Excel Writing configuration
     * 
     * available options :
     * - delimiter : (default = ';')  
     * - enclosure : (default = '"')  
     * - encoding : (default = 'CP1252')  
     * - eol : (default = "\r\n")  
     * - escape : (default = "\\")  
     * - bom : (default = false)  add UTF8 BOM marker
     * - translit : (default = 'translit')  iconv translit option possible values : 'translit', 'ignore', null
     * - trim : (default = false) trim each values on each line
     * 
     * @param array $options Dialect Options to describe CSV file parameters
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
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
        $enclosure = $this->dialect->getEnclosure();
        $escape = $this->dialect->getEscape();
        $trim = $this->dialect->getTrim();
        $line = implode($this->dialect->getDelimiter(), array_map(function($var) use ($enclosure, $escape, $trim) {
            // Escape enclosures and enclosed string
            return $enclosure.str_replace($enclosure, $escape.$enclosure, $trim ? trim($var) : $var).$enclosure;
        }, $values))
            // Add line ending
            .$this->dialect->getLineEndings();

        // Write to file
        fwrite($fileHandler, $this->convertEncoding($line, 'UTF-8', $this->dialect->getEncoding()));
        
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
