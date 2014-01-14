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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => false,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => false,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
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
                    'use_bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
                    'trim' => false,
                ),
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
        $this->reader = new CsvReader($options);
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
            //data set #0
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => false,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => false,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => true,
                    'skip_empty_lines' => false,
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
                    'use_bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
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
                    'use_bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
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
                    'use_bom' => true, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => false,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
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
                    'use_bom' => false, 
                    'translit' => 'translit',
                    'force_encoding_detection' => false,
                    'skip_empty_lines' => true,
                    'trim' => true,
                ),
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
