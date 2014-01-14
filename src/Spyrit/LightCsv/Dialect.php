<?php

namespace Spyrit\LightCsv;

/**
 * Dialect
 *
 * @author Charles Sanquer <charles.sanquer.ext@francetv.fr>
 */
class Dialect
{
    /**
     *
     * @var string
     */
    protected $translit;

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
     * @var bool
     */
    protected $useBom = false;
    
    /**
     *
     * @var bool
     */
    protected $trim = false;
    
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
     * Default Excel configuration
     *
     * @param string $delimiter default = ;
     * @param string $enclosure default = "
     * @param string $encoding  default = CP1252 default encoding
     * @param string $eol       default = "\r\n"
     * @param string $escape    default = "\\"
     * @param bool   $useBom    default = false (BOM will be handled when opening the file)
     * @param string $translit  default = "translit" (iconv translit option possible values : 'translit', 'ignore', null)
     */
    
    
    
    /**
     * available options :
     * - delimiter : (default = ';')  
     * - enclosure : (default = '"')  
     * - encoding : (default = 'CP1252')  
     * - eol : (default = "\r\n")  
     * - escape : (default = "\\")  
     * - use_bom : (default = false)  add UTF8 BOM marker
     * - translit : (default = 'translit')  iconv translit option possible values : 'translit', 'ignore', null
     * - force_encoding_detection : (default = false) 
     * - skip_empty_lines : (default = false)  remove lines with empty values
     * - trim : (default = false) trim each values on each line
     * 
     * @param array $options Dialect Options to describe CSV file parameters
     */
    public function __construct($options = array())
    {
        $options = is_array($options) ? $options : array();
        
        $cleanedOptions = array();
        foreach ($options as $key => $value) {
            $cleanedOptions[strtolower($key)] = $value;
        }
        
        $options = array_merge(array(
            'delimiter' => ';', 
            'enclosure' => '"', 
            'encoding' => 'CP1252', 
            'eol' => "\r\n", 
            'escape' => "\\", 
            'use_bom' => false, 
            'translit' => 'translit',
            'force_encoding_detection' => false,
            'skip_empty_lines' => false,
            'trim' => false,
        ), $cleanedOptions);
        
        $this->setDelimiter($options['delimiter']);
        $this->setEnclosure($options['enclosure']);
        $this->setEncoding($options['encoding']);
        $this->setLineEndings($options['eol']);
        $this->setEscape($options['escape']);
        $this->setTranslit($options['translit']);
        $this->setUseBom($options['use_bom']);
        $this->setTrim($options['trim']);
        $this->setForceEncodingDetection($options['force_encoding_detection']);
        $this->setSkipEmptyLines($options['skip_empty_lines']);
    }

    /**
     * return a CSV Dialect for Excel
     * 
     * @return Dialect
     */
    public static function createExcelDialect()
    {
        return new self(array(
            'delimiter' => ';', 
            'enclosure' => '"', 
            'encoding' => 'CP1252', 
            'eol' => "\r\n", 
            'escape' => "\\", 
            'use_bom' => false, 
            'translit' => 'translit',
            'force_encoding_detection' => false,
            'skip_empty_lines' => true,
            'trim' => false,
        ));
    }
    
    /**
     * return a standard CSV Dialect for unix with UTF-8
     * 
     * @return Dialect
     */
    public static function createStandardDialect()
    {
        return new self(array(
            'delimiter' => ',', 
            'enclosure' => '"', 
            'encoding' => 'UTF-8', 
            'eol' => "\n", 
            'escape' => "\\", 
            'use_bom' => false, 
            'translit' => 'translit',
            'force_encoding_detection' => false,
            'skip_empty_lines' => true,
            'trim' => true,
        ));
    }
    
    /**
     *
     * @param  string                       $eol
     * @return \Spyrit\LightCsv\Dialect
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
     * @return string
     */
    public function getTranslit()
    {
        return $this->translit;
    }

    /**
     *
     * @param  string                       $translit default = "translit" (iconv translit option possible values : 'translit', 'ignore', null)
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setTranslit($translit)
    {
        $translit = strtolower($translit);
        $this->translit = in_array($translit, array('translit', 'ignore')) ? $translit : null;

        return $this;
    }

    /**
     *
     * @param  string                       $encoding
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setEncoding($encoding)
    {
        $this->encoding = empty($encoding) ? 'CP1252' : $encoding;

        return $this;
    }

    /**
     *
     * @param  string                       $enclosure
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = empty($enclosure) ? '"' : $enclosure;

        return $this;
    }

    /**
     *
     * @param  string                       $escape
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setEscape($escape)
    {
        $this->escape = empty($escape) ? "\\" : $escape;

        return $this;
    }

    /**
     *
     * @param  string                       $delimiter
     * @return \Spyrit\LightCsv\Dialect
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
     * @return bool
     */
    public function getUseBom()
    {
        return $this->useBom;
    }

    /**
     *
     * @param bool $useBom (BOM will be writed when opening the file)
     *
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setUseBom($useBom)
    {
        $this->useBom = (bool) $useBom;

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function getTrim()
    {
        return $this->trim;
    }

    /**
     *
     * @param bool $trim (trim all values)
     *
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setTrim($trim)
    {
        $this->trim = (bool) $trim;

        return $this;
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
     * @return \Spyrit\LightCsv\Dialect
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
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setSkipEmptyLines($skipEmptyLines)
    {
        $this->skipEmptyLines = (bool) $skipEmptyLines;

        return $this;
    }
}
