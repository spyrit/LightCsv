<?php

namespace Spyrit\LightCsv;

use \Spyrit\LightCsv\AbstractCsv;
use \Spyrit\LightCsv\Utility\Converter;

/**
 * Csv Reader
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvReader extends AbstractCsv implements \Iterator, \Countable
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
     * @var string
     */
    protected $detectedEncoding;

    /**
     *
     * Default Excel Reading configuration
     * 
     * @param Dialect|array $options default = array()
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->fileHandlerMode = 'rb';
    }

    /**
     *
     * @param  string                     $filename
     * @return \Spyrit\LightCsv\CsvReader
     */
    public function open($filename = null)
    {
        parent::open($filename);
        $this->detectEncoding();

        return $this;
    }

    /**
     * Detect current file encoding if ForceEncodingDetection is set to true or encoding parameter is null
     */
    protected function detectEncoding()
    {
        $this->detectedEncoding = $this->dialect->getEncoding();
        if ($this->dialect->getForceEncodingDetection() || empty($this->dialect->detectedEncoding)) {
            $text = file_get_contents($this->getFilename());
            if ($text !== false) {
                $this->detectedEncoding = Converter::detectEncoding($text, $this->dialect->getEncoding());
            }
        }
    }

    /**
     *
     * @param  resource $fileHandler
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function readLine($fileHandler)
    {
        $row = null;
        if (!is_resource($fileHandler)) {
            throw new \InvalidArgumentException('A valid file handler resource must be passed as parameter');
        }

        if (!feof($fileHandler)) {
            $line = $this->convertEncoding($this->position == 0 ? $this->removeBom(fgets($fileHandler)) : fgets($fileHandler), $this->detectedEncoding, 'UTF-8');
            if ($line !== false) {
                $row = str_getcsv($line, $this->dialect->getDelimiter(), $this->dialect->getEnclosure(), $this->dialect->getEscape());
                if ($this->dialect->getTrim()) {
                    $row = array_map('trim', $row);
                }
                if ($this->dialect->getSkipEmptyLines() && count(array_filter($row, function($var) {
                    return $var !== false && $var !== null && $var !== '';
                })) === 0) {
                    $row = false;
                }
            }
        }

        return $row;
    }

    /**
     *
     * @return array
     */
    public function getRow()
    {
        if ($this->valid()) {
            $current = $this->current();
            $this->next();

            return $current;
        } else {
            return false;
        }
    }

    /**
     * reset CSV reading to 1st line
     *
     * aliases for iterator rewind
     */
    public function reset()
    {
        $this->rewind();
    }

    /******************************************************************************/
    /*                   iterator interface methods                               */
    /******************************************************************************/
    /**
     *
     * @return array
     */
    public function current()
    {
        return $this->currentValues;
    }

    /**
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->currentValues = $this->readLine($this->getFileHandler());
        $this->position++;

        if ($this->dialect->getSkipEmptyLines() && $this->currentValues === false) {
            $this->next();
        }
    }

    public function rewind()
    {
        if (!$this->isFileOpened()) {
            $this->openFile($this->fileHandlerMode);
        }

        $this->position = 0;
        rewind($this->getFileHandler());
        $this->currentValues = $this->readLine($this->getFileHandler());
        if ($this->dialect->getSkipEmptyLines() && $this->currentValues === false) {
            $this->next();
        }
    }

    /**
     *
     * @return bool
     */
    public function valid()
    {
        return $this->currentValues !== null;
    }

    /******************************************************************************/
    /*                   countable interface methods                              */
    /******************************************************************************/
    public function count()
    {
        if (!$this->isFileOpened()) {
            $this->openFile($this->fileHandlerMode);
        }

        $count = 0;
        rewind($this->getFileHandler());

        if ($this->dialect->getSkipEmptyLines()) {
            // empty row pattern without alphanumeric
            $pattern = '/(('.$this->dialect->getEnclosure().$this->dialect->getEnclosure().')?'.$this->dialect->getDelimiter().')+'.$this->dialect->getLineEndings().'/';
            $patternAlphaNum = '([[:alnum:]]+)';

            while (!feof($this->getFileHandler())) {
                $line = fgets($this->getFileHandler());
                if ($line !== null && $line != '' && $line !== $this->dialect->getLineEndings() && !(preg_match($pattern, $line) && !preg_match($patternAlphaNum, $line))) {
                    $count++;
                }
            }
        } else {
            while (!feof($this->getFileHandler())) {
                $line = fgets($this->getFileHandler());
                if ($line !== null && $line != '') {
                    $count++;
                }
            }
        }

        return $count;
    }
}
