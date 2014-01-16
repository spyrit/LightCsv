<?php

namespace Spyrit\LightCsv;

/**
 * Dialect
 *
 * @author Charles Sanquer <charles.sanquer.ext@francetv.fr>
 */
class Dialect
{
    const ENCLOSING_ALL = 'all';
    const ENCLOSING_MINIMAL = 'minimal';
    const ENCLOSING_NONNUMERIC = 'nonnumeric';
    
    protected static $defaultOptions = array(
        'excel' => array(
            'delimiter' => ';', 
            'enclosure' => '"', 
            'enclosing_mode' => 'minimal',
            'encoding' => 'CP1252', 
            'eol' => "\r\n", 
            'escape' => "\\", 
            'escape_double' => true,
            'bom' => false, 
            'translit' => 'translit',
            'force_encoding_detect' => false,
            'skip_empty' => false,
            'trim' => false,
        ),
        'unix' => array(
            'delimiter' => ',', 
            'enclosure' => '"', 
            'enclosing_mode' => 'minimal',
            'encoding' => 'UTF-8', 
            'eol' => "\n", 
            'escape' => "\\",
            'escape_double' => true,
            'bom' => false, 
            'translit' => 'translit',
            'force_encoding_detect' => false,
            'skip_empty' => false,
            'trim' => false,
        ),
    );
    
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
    protected $enclosingMode;
    
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
     * @var bool
     */
    protected $escapeDouble;
    
    /**
     *
     * @var string
     */
    protected $delimiter;

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
     * @var bool
     */
    protected $skipEmptyLines;

    /**
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
        $options = is_array($options) ? $options : array();
        
        $cleanedOptions = array();
        foreach ($options as $key => $value) {
            $cleanedOptions[strtolower($key)] = $value;
        }
        
        $options = array_merge(static::getDialectDefaultOptions('excel'), $cleanedOptions);
        
        $this->setDelimiter($options['delimiter']);
        $this->setEnclosure($options['enclosure']);
        $this->setEncoding($options['encoding']);
        $this->setEncoding($options['encoding']);
        $this->setEnclosingMode($options['enclosing_mode']);
        $this->setLineEndings($options['eol']);
        $this->setEscape($options['escape']);
        $this->setEscapeDouble($options['escape_double']);
        $this->setTranslit($options['translit']);
        $this->setUseBom($options['bom']);
        $this->setTrim($options['trim']);
        $this->setForceEncodingDetect($options['force_encoding_detect']);
        $this->setSkipEmptyLines($options['skip_empty']);
    }

    /**
     * get Default CSV options for a specific CSV reader application like Excel
     * 
     * @param string $CSVType default = excel
     * 
     * @return array 
     */
    public static function getDialectDefaultOptions($CSVType = 'excel')
    {
        return isset(static::$defaultOptions[$CSVType]) ? static::$defaultOptions[$CSVType] : array();
    }
    
    /**
     * return a CSV Dialect for Excel
     * 
     * @return Dialect
     */
    public static function createExcelDialect()
    {
        return new self(static::getDialectDefaultOptions('excel'));
    }
    
    /**
     * return a standard CSV Dialect for unix with UTF-8
     * 
     * @return Dialect
     */
    public static function createUnixDialect()
    {
        return new self(static::getDialectDefaultOptions('unix'));
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
     * @return string
     */
    public function getEnclosingMode()
    {
        return $this->enclosingMode;
    }

    /**
     * 
     * @param string $enclosingMode
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setEnclosingMode($enclosingMode)
    {
        $this->enclosingMode = in_array($enclosingMode, array(
            static::ENCLOSING_ALL,
            static::ENCLOSING_MINIMAL,
            static::ENCLOSING_NONNUMERIC,
        )) ? $enclosingMode : static::ENCLOSING_MINIMAL;
        
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getEscapeDouble()
    {
        return $this->escapeDouble;
    }

    /**
     * 
     * @param bool $escapeDouble
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setEscapeDouble($escapeDouble)
    {
        $this->escapeDouble = (bool) $escapeDouble;
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
    public function getForceEncodingDetect()
    {
        return $this->forceEncodingDetect;
    }

    /**
     *
     * @param  bool                       $forceEncodingDetect
     * @return \Spyrit\LightCsv\Dialect
     */
    public function setForceEncodingDetect($forceEncodingDetect)
    {
        $this->forceEncodingDetect = (bool) $forceEncodingDetect;

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
