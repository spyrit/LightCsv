<?php

namespace Spyrit\Csv;

use Spyrit\Csv\AbstractCsv;
use Spyrit\Csv\Utility\Converter;

/**
 * Csv Writer
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvWriter extends AbstractCsv
{
    protected $useBom = false;

    /**
     *
     * @param string $delimiter default = ;
     * @param string $enclosure default = "
     * @param string $encoding  default = CP1252
     * @param string $eol       default = "\r\n"
     * @param string $escape    default = "\\"
     * @param bool   $useBom    default = false
     */
    public function __construct($delimiter = ';', $enclosure = '"', $encoding = 'CP1252', $eol = "\r\n", $escape = "\\", $useBom = false)
    {
        parent::__construct($delimiter, $enclosure, $encoding, $eol, $escape);
        $this->fileHandlerMode = 'wb';
        $this->setUseBom($useBom);
    }

    /**
     *
     * @return bool
     */
    public function getUseBom()
    {
        return $this->useBom;
    }

    /**
     *
     * @param bool $useBom
     *
     * @return \Spyrit\Csv\CsvWriter
     */
    public function setUseBom($useBom)
    {
        $this->useBom = (bool) $useBom;

        return $this;
    }

    /**
     * Write UTF-8 BOM code if encoding is UTF-8 and useBom is set to true
     *
     * @return \Spyrit\Csv\CsvWriter
     */
    public function writeBom()
    {
        if ($this->useBom && $this->encoding == 'UTF-8') {
            // Write the UTF-8 BOM code
            fwrite($this->fileHandler, "\xEF\xBB\xBF");
        }

        return $this;
    }


    /**
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
     * echo headers for CSV
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
     * @return \Spyrit\Csv\CsvWriter
     *
     * @throws \InvalidArgumentException
     */
    protected function writeLine($fileHandler, $values)
    {
        $line = '';

        $first = true;
        foreach ($values as $value) {
            // Escape enclosures
            $value = str_replace($this->enclosure, $this->escape.$this->enclosure, $value);

            if ($this->encoding !== 'UTF-8') {
                $value = Converter::convertEncoding($value, 'UTF-8', $this->encoding);
            }

            // Add delimiter
            if (!$first) {
                $line .= $this->delimiter;
            } else {
                $first = false;
            }

            // Add enclosed string
            $line .= $this->enclosure.$value.$this->enclosure;
        }

        // Add line ending
        $line .= $this->eol;

        // Write to file
        fwrite($fileHandler, $line);

        return $this;
    }

    /**
     *
     * @param array $values
     *
     * @return \Spyrit\Csv\CsvWriter
     */
    public function addLine(array $values)
    {
        if (!$this->isFileOpened()) {
            $this->openFile($this->fileHandlerMode);
        }

        return $this->writeLine($this->getFileHandler(), $values);
    }

    /**
     *
     * @param array $lines
     *
     * @return \Spyrit\Csv\CsvWriter
     */
    public function addLines(array $lines)
    {
        foreach ($lines as $values) {
            $this->addLine($values);
        }

        return $this;
    }
}
