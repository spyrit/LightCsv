<?php

namespace Spyrit\LightCsv\Tests\Csv;

use Spyrit\LightCsv\Tests\AbstractCsvTestCase;
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
        $this->reader = new CsvReader($options);
        $this->reader->setFilename($filename);
        $this->assertEquals($expected, count($this->reader));
    }

    public function providerCount()
    {
        return array(
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test1.csv',
                3
            ),
            array(
                array(
                    'delimiter' => ';', 
                    'enclosure' => '"', 
                    'encoding' => 'CP1252', 
                    'eol' => "\r\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test2.csv',
                4
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test3.csv',
                3
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test4.csv',
                4
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test5_bom.csv',
                3
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test6.csv',
                3
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'double_enclosure' => true,
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test7.csv',
                4
            ),
        );
    }

    /**
     * @dataProvider providerReading
     */
    public function testReading($options, $filename, $expected)
    {
        $this->reader = new CsvReader($options);
        $this->assertInstanceOf('Spyrit\LightCsv\CsvReader', $this->reader->open($filename));

        $actual1 = array();
        $i = 0;
        foreach ($this->reader as $key => $value) {
            $i++;
            $actual1[] = $value;
        }

        $actual2 = $this->reader->getRows();
        
        $this->reader->reset();
        $actual3 = array();
        while ($row = $this->reader->getRow()) {
            $actual3[] = $row;
        }

        $this->assertEquals($expected, $actual1);
        $this->assertEquals($expected, $actual2);
        $this->assertEquals($expected, $actual3);
        $this->assertInstanceOf('Spyrit\LightCsv\CsvReader', $this->reader->close());
    }

    public function providerReading()
    {
        return array(
            //data set #0
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test1.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            //data set #1
            array(
                array(
                    'delimiter' => ';', 
                    'enclosure' => '"', 
                    'encoding' => 'CP1252', 
                    'eol' => "\r\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test2.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Bousquet', 'Inès', '32'),
                    array('Morel', 'Monique', '41'),
                    array('Gauthier', 'Aurélie', '24'),
                )
            ),
            //data set #2
            array(
                array(
                    'delimiter' => ';', 
                    'enclosure' => '"', 
                    'encoding' => '', 
                    'eol' => "\r\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => true,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test2.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Bousquet', 'Inès', '32'),
                    array('Morel', 'Monique', '41'),
                    array('Gauthier', 'Aurélie', '24'),
                )
            ),
            //data set #3
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test3.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            //data set #4
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test4.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                    array('Dupont', '', ''),
                )
            ),
            //data set #5
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test5_bom.csv', //file UTF8 with BOM
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            //data set #6
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test6.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', '  Richard ', '36  '),
                )
            ),
            //data set #7
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => true,
                    'trim' => true,
                ),
                __DIR__.'/../Fixtures/test6.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28'),
                    array('Alain', 'Richard', '36'),
                )
            ),
            //data set #8
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'double_enclosure' => true,
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test7.csv',
                array(
                    array('nom', 'prénom', 'desc', 'age'),
                    array('Martin', 'Durand
 test', '"5\'10""', '28'),
                    array('Alain', 'Richard', '"5\'30""', '36'),
                    array('Paul', 'Henri', '"4\'80""','22'),
                )
            ),
            //data set #9
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detect' => false,
                    'skip_empty' => false,
                    'trim' => false,
                ),
                __DIR__.'/../Fixtures/test8.csv',
                array(
                    array('nom', 'prénom', 'desc', 'age'),
                    array('Martin', 'Durand', 'test" a', '28'),
                    array('Alain', 'Richard', 'test"" b', '36'),
                )
            ),
        );
    }
}
