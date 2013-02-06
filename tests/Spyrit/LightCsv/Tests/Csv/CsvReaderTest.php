<?php

namespace Spyrit\LightCsv\Tests\Csv;

use Spyrit\LightCsv\Tests\Test\AbstractCsvTestCase;
use Spyrit\LightCsv\CsvReader;

/**
 * CsvReaderTest
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvReaderTest extends AbstractCsvTestCase
{
    /**
     *
     * @var \Spyrit\LightCsv\CsvReader
     */
    protected $reader;

    protected function setUp()
    {
        $this->reader = new CsvReader();
    }

    public function testConstruct()
    {
        $this->assertEquals('rb', $this->getFileHandlerModeValue($this->reader));
        $this->assertEquals(';', $this->reader->getDelimiter());
        $this->assertEquals('"', $this->reader->getEnclosure());
        $this->assertEquals('CP1252', $this->reader->getEncoding());
        $this->assertEquals("\r\n", $this->reader->getLineEndings());
        $this->assertEquals("\\", $this->reader->getEscape());
        $this->assertFalse($this->reader->getDetectEncoding());
    }

    /**
     * @dataProvider providerGetSetDetectEncoding
     */
    public function testGetSetDetectEncoding($input,$expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv',$this->reader->setDetectEncoding($input));
        $this->assertEquals($expected,$this->reader->getDetectEncoding());
    }

    public function providerGetSetDetectEncoding()
    {
        return array(
            array(null,false),
            array(false,false),
            array(true,true),
            array(0,false),
            array('0',false),
            array(1,true),
            array('1',true),
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReadingNoFilename()
    {
        $actual = array();
        foreach ($this->reader as $key => $value) {
            $actual[] = $value;
        }
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReadingNoFileHandler()
    {
        $this->reader->next();
    }

    /**
     * @dataProvider providerCount
     */
    public function testCount($options, $filename, $expected)
    {
        $this->reader = new CsvReader($options[0], $options[1], $options[2], $options[3]);
        $this->reader->setFilename($filename);
        $this->assertEquals($expected, count($this->reader));
    }

    public function providerCount()
    {
        return array(
            array(
                array(',','"', 'UTF-8', "\n"),
                __DIR__.'/../Fixtures/test1.csv',
                3
            ),
            array(
                array(';','"', 'CP1252', "\n"),
                __DIR__.'/../Fixtures/test2.csv',
                4
            ),
        );
    }
    /**
     * @dataProvider providerReading
     */
    public function testReading($options, $filename, $expected)
    {
        $this->reader = new CsvReader($options[0], $options[1], $options[2], $options[3]);
        $this->assertInstanceOf('Spyrit\LightCsv\CsvReader',$this->reader->open($filename));

        $actual1 = array();
        $i = 0;
        foreach ($this->reader as $key => $value) {
            $this->assertEquals($i, $key);
            $i++;
            $actual1[] = $value;
        }

        $this->reader->reset();
        $actual2 = array();
        while ($row = $this->reader->getRow()) {
            $actual2[] = $row;
        }

        $this->assertEquals($expected, $actual1);
        $this->assertEquals($expected, $actual2);
        $this->assertInstanceOf('Spyrit\LightCsv\CsvReader',$this->reader->close());
    }

    public function providerReading()
    {
        return array(
            array(
                array(',','"', 'UTF-8', "\n"),
                __DIR__.'/../Fixtures/test1.csv',
                array(
                    array('nom','prénom','age'),
                    array('Martin','Durand','28'),
                    array('Alain','Richard','36'),
                )
            ),
            array(
                array(';','"', 'CP1252', "\n"),
                __DIR__.'/../Fixtures/test2.csv',
                array(
                    array('nom','prénom','age'),
                    array('Bousquet', 'Inès' ,'32'),
                    array('Morel','Monique','41'),
                    array('Gauthier','Aurélie','24'),
                )
            ),
        );
    }
}
