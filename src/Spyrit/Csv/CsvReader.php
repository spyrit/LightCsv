<?php

namespace Spyrit\Csv;

use Spyrit\Csv\AbstractCsv;
use Spyrit\Csv\Utility\Converter;

/**
 * Csv Reader
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvReader extends AbstractCsv implements \Iterator
{
    /**
     *
     * @var int
     */
    private $position = 0;

    /**
     *
     * @var array
     */
    private $currentValues = array();

    /**
     *
     * Default Excel Reading configuration
     *
     * @param string $delimiter default = ;
     * @param string $enclosure default = "
     * @param string $encoding  default = CP1252 (csv rows will be converted from this encoding)
     * @param string $eol       default = "\r\n"
     * @param string $escape    default = "\\"
     */
    public function __construct($delimiter = ';', $enclosure = '"', $encoding = 'CP1252', $eol = "\r\n", $escape = "\\")
    {
        parent::__construct($delimiter, $enclosure, $encoding, $eol, $escape);
        $this->fileHandlerMode = 'rb';
    }

    protected function readLine($fileHandler)
    {
        $result = null;
        if (!is_resource($fileHandler)) {
            throw new \InvalidArgumentException('A valid file handler resource must be passed as parameter');
        }

        if (!feof($fileHandler)) {
            $escapes = array($this->escape.$this->enclosure, $this->enclosure.$this->enclosure);

            $row = fgetcsv($fileHandler, 0, $this->delimiter, $this->enclosure);
            if ($row !== false) {
                $result = array();
                foreach ($row as $value) {
                    // Unescape enclosures
                    $value = str_replace($escapes, $this->enclosure, $value);
                    // Convert encoding if necessary
                    if ($this->encoding !== 'UTF-8') {
                        $value = Converter::convertEncoding($value, $this->encoding, 'UTF-8');
                    }
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    // iterator methods for reading CSV
    public function current()
    {
        return $this->currentValues;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
        $this->currentValues = $this->readLine($this->getFileHandler());
    }

    public function rewind()
    {
        if ($this->isFileOpened()) {
            $this->position = 0;
            rewind($this->getFileHandler());
        }

        $this->currentValues = $this->readLine($this->getFileHandler());
    }

    public function valid()
    {
        return $this->currentValues !== null;
    }
}
