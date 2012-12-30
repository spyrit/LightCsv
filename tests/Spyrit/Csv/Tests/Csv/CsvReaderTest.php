<?php

namespace Spyrit\Csv\Tests\Csv;

use Spyrit\Csv\Tests\Test\AbstractCsvTestCase;
use Spyrit\Csv\CsvReader;

/**
 * CsvReaderTest
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvReaderTest extends AbstractCsvTestCase
{
    /**
     *
     * @var \Spyrit\Csv\CsvReader
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
     * @dataProvider providerReading
     */
    public function testReading($options, $filename, $expected)
    {
        $this->reader = new CsvReader($options[0], $options[1], $options[2], $options[3]);
        $this->assertInstanceOf('Spyrit\Csv\CsvReader',$this->reader->open($filename));

        $actual = array();
        foreach ($this->reader as $key => $value) {
            $actual[] = $value;
        }

        $this->assertEquals($expected, $actual);
        $this->assertInstanceOf('Spyrit\Csv\CsvReader',$this->reader->close());
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
