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
     * available options :
     * - delimiter : (default = ';')  
     * - enclosure : (default = '"')  
     * - encoding : (default = 'CP1252')  
     * - eol : (default = "\r\n")  
     * - escape : (default = "\\")  
     * - bom : (default = false)  add UTF8 BOM marker
     * - translit : (default = 'translit')  iconv translit option possible values : 'translit', 'ignore', null
     * - force_encoding_detect : (default = false) 
     * - skip_empty : (default = false)  remove lines with empty values
     * - trim : (default = false) trim each values on each line
     * 
     * @param array $options Dialect Options to describe CSV file parameters
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
        if ($this->dialect->getForceEncodingDetect() || empty($this->dialect->detectedEncoding)) {
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
            $enclosure = $this->dialect->getEnclosure();
            $escape = $this->dialect->getEscape();
            $line = fgetcsv($fileHandler, null, $this->dialect->getDelimiter(), $enclosure, $escape);
                   
            if ($line !== false) {
                $trim = $this->dialect->getTrim();
                $translit = $this->dialect->getTranslit();
                $detectedEncoding = $this->detectedEncoding;
                
                if ($this->position == 0) {
                    $line[0] = $this->removeBom($line[0]);
                }
                
                $row = array_map(function($var) use ($enclosure, $escape, $trim, $translit, $detectedEncoding) {
                    // workaround when escape char is not equals to double quote
                    if ($enclosure === '"' && $escape !== $enclosure) {
                        $var = str_replace($escape.$enclosure, $enclosure, $var);
                    }

                    $var = Converter::convertEncoding($var, $detectedEncoding, 'UTF-8', $translit);
                    return $trim ? trim($var) : $var;
                }, $line);
                
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

        $enclosure = $this->dialect->getEnclosure();
        $escape = $this->dialect->getEscape();
        $delimiter = $this->dialect->getDelimiter();
        
        if ($this->dialect->getSkipEmptyLines()) {
            while (!feof($this->getFileHandler())) {
                $line = fgetcsv($this->getFileHandler(), null, $delimiter, $enclosure, $escape);
                if (!empty($line) && count(array_filter($line, function($var) {
                    // empty row pattern without alphanumeric
                    return $var !== false && $var !== null && $var !== '' && preg_match('([[:alnum:]]+)', $var);
                })) !== 0) {
                    $count++;
                }
            }
        } else {
            while (!feof($this->getFileHandler())) {
                $line = fgetcsv($this->getFileHandler(), null, $delimiter, $enclosure, $escape);
                if (!empty($line)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
