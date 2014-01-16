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
        $enclosingMode = $this->dialect->getEnclosingMode();
        $escapeDouble = $this->dialect->getEscapeDouble();
        $line = implode($this->dialect->getDelimiter(), array_map(function($var) use ($enclosure, $escape, $trim, $enclosingMode, $escapeDouble) {
            // Escape enclosures and enclosed string
            if ($escapeDouble) {
                // double enclosure
                $searches = array($enclosure);
                $replacements = array($enclosure.$enclosure);
            } else {
                // use escape character
                $searches = array($enclosure);
                $replacements = array($escape.$enclosure);
            }
            $clean = str_replace($searches, $replacements, $trim ? trim($var) : $var);
            
            if (
                $enclosingMode === Dialect::ENCLOSING_ALL || 
                ($enclosingMode === Dialect::ENCLOSING_MINIMAL && preg_match('/['.preg_quote($this->dialect->getEnclosure().$this->dialect->getDelimiter().$this->dialect->getLineEndings(), '/').']+/', $clean)) ||
                ($enclosingMode === Dialect::ENCLOSING_NONNUMERIC && preg_match('/[^\d\.]+/', $clean)) 
            )
            {
                $var = $enclosure.$clean.$enclosure;
            } else {
                $var = $clean;
            }
            
            return $var;
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
