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
     * @var bool
     */
    protected $forceEncodingDetection;

    /**
     *
     * @var string
     */
    protected $detectedEncoding;

    /**
     *
     * @var bool
     */
    protected $skipEmptyLines;

    /**
     *
     * Default Excel Reading configuration
     *
     * @param string $delimiter              default = ;
     * @param string $enclosure              default = "
     * @param string $encoding               default = CP1252  default encoding if not detected (csv rows will be converted from this encoding)
     * @param string $eol                    default = "\r\n"
     * @param string $escape                 default = "\\"
     * @param bool   $useBom                 default = false (BOM will be removed when opening the file)
     * @param string $translit               default = "translit" (iconv translit option possible values : 'translit', 'ignore', null)
     * @param bool   $forceEncodingDetection default = false
     * @param bool   $skipEmptyLines         default = false
     */
    public function __construct($delimiter = ';', $enclosure = '"', $encoding = 'CP1252', $eol = "\r\n", $escape = "\\", $useBom = false, $translit = 'translit', $forceEncodingDetection = false, $skipEmptyLines = false)
    {
        parent::__construct($delimiter, $enclosure, $encoding, $eol, $escape, $useBom, $translit);
        $this->setForceEncodingDetection($forceEncodingDetection);
        $this->setSkipEmptyLines($skipEmptyLines);
        $this->fileHandlerMode = 'rb';
    }

    /**
     *
     * @return bool
     */
    public function getForceEncodingDetection()
    {
        return $this->forceEncodingDetection;
    }

    /**
     *
     * @param  bool                       $forceEncodingDetection
     * @return \Spyrit\LightCsv\CsvReader
     */
    public function setForceEncodingDetection($forceEncodingDetection)
    {
        $this->forceEncodingDetection = (bool) $forceEncodingDetection;

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function getSkipEmptyLines()
    {
        return $this->skipEmptyLines;
    }

    /**
     *
     * @param  bool                       $skipEmptyLines
     * @return \Spyrit\LightCsv\CsvReader
     */
    public function setSkipEmptyLines($skipEmptyLines)
    {
        $this->skipEmptyLines = (bool) $skipEmptyLines;

        return $this;
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
        $this->detectedEncoding = $this->getEncoding();
        if ($this->forceEncodingDetection || empty($this->detectedEncoding)) {
            $text = file_get_contents($this->getFilename());
            if ($text !== false) {
                $this->detectedEncoding = Converter::detectEncoding($text, $this->getEncoding());
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
        $result = null;
        if (!is_resource($fileHandler)) {
            throw new \InvalidArgumentException('A valid file handler resource must be passed as parameter');
        }

        if (!feof($fileHandler)) {
            $line = $this->convertEncoding($this->removeBom(fgets($fileHandler)), $this->detectedEncoding, 'UTF-8');
            if ($line !== false) {
                $row = str_getcsv($line, $this->delimiter, $this->enclosure, $this->escape);
                $result = array();
                $empty = true;
                foreach ($row as $value) {
                    if (!empty($value)) {
                        $empty = false;
                    }
                    $result[] = $value;
                }

                if ($this->skipEmptyLines && $empty) {
                    $result = array();
                }
            }
        }

        return $result;
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
    /*     * *************************************************************************** */
    /*                   iterator interface methods                               */
    /*     * *************************************************************************** */

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

        if ($this->skipEmptyLines && is_array($this->currentValues) && empty($this->currentValues)) {
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
    }

    /**
     *
     * @return bool
     */
    public function valid()
    {
        return $this->currentValues !== null;
    }
    /*     * *************************************************************************** */
    /*                   countable interface methods                               */
    /*     * *************************************************************************** */

    public function count()
    {
        if (!$this->isFileOpened()) {
            $this->openFile($this->fileHandlerMode);
        }

        $count = 0;
        rewind($this->getFileHandler());

        if ($this->skipEmptyLines) {
            // empty row pattern without alphanumeric
            $pattern = '/(('.$this->enclosure.$this->enclosure.')?'.$this->delimiter.')+'.$this->eol.'/';
            $patternAlphaNum = '([[:alnum:]]+)';

            while (!feof($this->getFileHandler())) {
                $line = fgets($this->getFileHandler());
                if ($line !== null && $line != '' && $line !== $this->eol && !(preg_match($pattern, $line) && !preg_match($patternAlphaNum, $line))) {
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
