<?php

namespace Spyrit\LightCsv;

/**
 * Common Abstract Csv
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
abstract class AbstractCsv
{
    /**
     *
     * @var string
     */
    protected $eol;

    /**
     *
     * @var string
     */
    protected $encoding;

    /**
     *
     * @var string
     */
    protected $enclosure;

    /**
     *
     * @var string
     */
    protected $escape;

    /**
     *
     * @var string
     */
    protected $delimiter;

    /**
     *
     * @var string
     */
    protected $filename;

    /**
     *
     * @var string
     */
    protected $fileHandlerMode;

    /**
     *
     * @var resource
     */
    protected $fileHandler;

    /**
     *
     * Default Excel configuration
     *
     * @param string $delimiter default = ;
     * @param string $enclosure default = "
     * @param string $encoding  default = CP1252
     * @param string $eol       default = "\r\n"
     * @param string $escape    default = "\\"
     */
    public function __construct($delimiter = ';', $enclosure = '"', $encoding = 'CP1252', $eol = "\r\n", $escape = "\\")
    {
        $this->setDelimiter($delimiter);
        $this->setEnclosure($enclosure);
        $this->setEncoding($encoding);
        $this->setLineEndings($eol);
        $this->setEscape($escape);
    }

    public function __destruct()
    {
        $this->closeFile();
    }

    /**
     *
     * @param  string                  $filename
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     *
     * @param  string                  $eol
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function setLineEndings($eol)
    {
        switch ($eol) {
            case 'unix':
            case 'linux';
            case "\n";
            default:
                $this->eol = "\n";
                break;

            case 'mac':
            case 'macos';
            case "\r";
            default:
                $this->eol = "\r";
                break;

            case 'windows':
            case 'win';
            case "\r\n";
            default:
                $this->eol = "\r\n";
                break;
        }

        return $this;
    }

    /**
     *
     * @param  string                  $encoding
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function setEncoding($encoding)
    {
        $this->encoding = empty($encoding) ? 'CP1252' : $encoding;

        return $this;
    }

    /**
     *
     * @param  string                  $enclosure
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = empty($enclosure) ? '"' : $enclosure;

        return $this;
    }

    /**
     *
     * @param  string                  $escape
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function setEscape($escape)
    {
        $this->escape = empty($escape) ? "\\" : $escape;

        return $this;
    }

    /**
     *
     * @param  string                  $delimiter
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = empty($delimiter) ? ';' : $delimiter;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLineEndings()
    {
        return $this->eol;
    }

    /**
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     *
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     *
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     *
     * @param  string   $mode file handler open mode, default = rb
     * @return resource file handler
     *
     * @throws \InvalidArgumentException
     */
    protected function openFile($mode = 'rb')
    {
        $mode = empty($mode) ? 'rb' : $mode;

        if (is_null($this->filename) || $this->filename == '') {
            throw new \InvalidArgumentException('the filename is not valid');
        }

        $this->fileHandler = @fopen($this->filename, $mode);
        if ($this->fileHandler === false) {
            $modeLabel = (strpos('r', $mode) !== false && strpos('+', $mode) === false) ? 'reading' : 'writing';
            throw new \InvalidArgumentException('Could not open file '.$this->filename.' for '.$modeLabel.'.');
        }

        return $this->fileHandler;
    }

    /**
     *
     * @return boolean
     */
    protected function closeFile()
    {
        if ($this->isFileOpened()) {
            $ret = @fclose($this->fileHandler);
            $this->fileHandler = null;

            return $ret;
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    protected function isFileOpened()
    {
        return is_resource($this->fileHandler);
    }

    /**
     *
     * @return resource
     */
    protected function getFileHandler()
    {
        return $this->fileHandler;
    }

    /**
     * open a csv file to read or write
     *
     * @param  string                  $filename default = null
     * @return \Spyrit\LightCsv\AbstractCsv
     * 
     * @throws \InvalidArgumentException
     */
    public function open($filename = null)
    {
        if (!is_null($filename) && $filename != '' ) {
            $this->setFilename($filename);
        }

        $this->openFile($this->fileHandlerMode);

        return $this;
    }

    /**
     * close the current csv file
     *
     * @return \Spyrit\LightCsv\AbstractCsv
     */
    public function close()
    {
        $this->closeFile();

        return $this;
    }
}
