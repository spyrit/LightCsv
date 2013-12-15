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
        $this->assertFalse($this->reader->getForceEncodingDetection());
    }

    /**
     * @dataProvider providerGetSetForceEncodingDetection
     */
    public function testGetSetForceEncodingDetection($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->reader->setForceEncodingDetection($input));
        $this->assertEquals($expected, $this->reader->getForceEncodingDetection());
    }

    public function providerGetSetForceEncodingDetection()
    {
        return array(
            array(null, false),
            array(false, false),
            array(true, true),
            array(0, false),
            array('0', false),
            array(1, true),
            array('1', true),
        );
    }

    /**
     * @dataProvider providerGetSetSkipEmptyLines
     */
    public function testGetSetSkipEmptyLines($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->reader->setSkipEmptyLines($input));
        $this->assertEquals($expected, $this->reader->getSkipEmptyLines());
    }

    public function providerGetSetSkipEmptyLines()
    {
        return array(
            array(null, false),
            array(false, false),
            array(true, true),
            array(0, false),
            array('0', false),
            array(1, true),
            array('1', true),
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
     * @expectedException \InvalidArgumentException
     */
    public function testReadingFilenameInvalid()
    {
        $this->reader->open('foobar.csv');
    }

    /**
     * @dataProvider providerCount
     */
    public function testCount($options, $filename, $expected)
    {
        $this->reader = new CsvReader($options[0], $options[1], $options[2], $options[3], $options[4], $options[5], $options[6], $options[7], $options[8]);
        $this->reader->setFilename($filename);
        $this->assertEquals($expected, count($this->reader));
    }

    public function providerCount()
    {
        return array(
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, false),
                __DIR__.'/../Fixtures/test1.csv',
                3
            ),
            array(
                array(';', '"', 'CP1252', "\r\n", "\\", false, 'translit', false, false),
                __DIR__.'/../Fixtures/test2.csv',
                4
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, true),
                __DIR__.'/../Fixtures/test3.csv',
                3
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, true),
                __DIR__.'/../Fixtures/test4.csv',
                4
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", true, 'translit', false, true),
                __DIR__.'/../Fixtures/test5_bom.csv',
                3
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, true),
                __DIR__.'/../Fixtures/test6.csv',
                3
            ),
        );
    }

    /**
     * @dataProvider providerReading
     */
    public function testReading($options, $filename, $expected)
    {
        $this->reader = new CsvReader($options[0], $options[1], $options[2], $options[3], $options[4], $options[5], $options[6], $options[7], $options[8]);
        $this->assertInstanceOf('Spyrit\LightCsv\CsvReader', $this->reader->open($filename));

        $actual1 = array();
        $i = 0;
        foreach ($this->reader as $key => $value) {
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
        $this->assertInstanceOf('Spyrit\LightCsv\CsvReader', $this->reader->close());
    }

    public function providerReading()
    {
        return array(
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, false),
                __DIR__.'/../Fixtures/test1.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            array(
                array(';', '"', 'CP1252', "\r\n", "\\", false, 'translit', false, false),
                __DIR__.'/../Fixtures/test2.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Bousquet', 'Inès', '32'),
                    array('Morel', 'Monique', '41'),
                    array('Gauthier', 'Aurélie', '24'),
                )
            ),
            array(
                array(';', '"', '', "\r\n", "\\", false, 'translit', true, false),
                __DIR__.'/../Fixtures/test2.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Bousquet', 'Inès', '32'),
                    array('Morel', 'Monique', '41'),
                    array('Gauthier', 'Aurélie', '24'),
                )
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, true),
                __DIR__.'/../Fixtures/test3.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, true),
                __DIR__.'/../Fixtures/test4.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                    array('Dupont', '', ''),
                )
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", true, 'translit', false, false),
                __DIR__.'/../Fixtures/test5_bom.csv', //file UTF8 with BOM
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            array(
                array(',', '"', 'UTF-8', "\n", "\\", false, 'translit', false, true),
                __DIR__.'/../Fixtures/test6.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
        );
    }
}
