<?php

namespace Spyrit\LightCsv\Tests\Csv;

use Spyrit\LightCsv\CsvWriter;
use Spyrit\LightCsv\Dialect;
use Spyrit\LightCsv\Tests\AbstractCsvTestCase;

/**
 * CsvWriterTest
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvWriterTest extends AbstractCsvTestCase
{
    /**
     *
     * @var CsvWriter
     */
    protected $writer;

    protected function setUp()
    {
        $this->writer = new CsvWriter();
    }

    public function testConstruct()
    {
        $this->assertEquals('wb', $this->getFileHandlerModeValue($this->writer));
    }

    /**
     * @dataProvider providerWritingRow
     */
    public function testWritingRow($options, $filename, $row, $expectedCsv)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        $this->writer = new CsvWriter($options);

        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->open($filename));
        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->writeRow($row));
        $this->assertEquals($expectedCsv, file_get_contents($filename));
        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->close());

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function providerWritingRow()
    {
        return array(
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => true,
                ),
                __DIR__.'/../Fixtures/testWrite0.csv',
                array('Martin', 'Durand', 'test " abc', '28,5'),
                'Martin,Durand,"test "" abc","28,5"'."\n",
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => false,
                ),
                __DIR__.'/../Fixtures/testWrite0.csv',
                array('Martin', 'Durand', 'test " abc','28,5'),
                'Martin,Durand,"test \" abc","28,5"'."\n",
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'enclosing_mode' => Dialect::ENCLOSING_ALL,
                    'escape_double' => true,
                ),
                __DIR__.'/../Fixtures/testWrite0.csv',
                array('Martin', 'Durand', '28.5'),
                '"Martin","Durand","28.5"'."\n",
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'enclosing_mode' => Dialect::ENCLOSING_NONNUMERIC,
                    'escape_double' => true,
                ),
                __DIR__.'/../Fixtures/testWrite0.csv',
                array('Martin', 'Durand', '28.5'),
                '"Martin","Durand",28.5'."\n",
            ),
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'trim' => true,
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => true,
                ),
                __DIR__.'/../Fixtures/testWrite1.csv',
                array('  Martin ', 'Durand  ', '  28,5'),
                'Martin,Durand,"28,5"'."\n",
            ),
            array(
                array(
                    'delimiter' => ';', 
                    'enclosure' => '"', 
                    'encoding' => 'CP1252', 
                    'eol' => "\r\n", 
                    'escape' => "\\", 
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => true,
                ),
                __DIR__.'/../Fixtures/testWrite2.csv',
                array('Gauthier', 'Aurélie', '24'),
                mb_convert_encoding('Gauthier;Aurélie;24'."\r\n", 'CP1252', 'UTF-8'),
            ),
        );
    }

    /**
     * @dataProvider providerWritingRows
     */
    public function testWritingRows($options, $filename, $rows, $expectedCsv)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        $this->writer = new CsvWriter($options);

        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->setFilename($filename));
        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->writeRows($rows));
        $this->assertEquals($expectedCsv, file_get_contents($filename));
        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->close());

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function providerWritingRows()
    {
        return array(
            // Data set #0
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => true,
                    'eol' => "\n", 
                    'escape' => "\\", 
                ),
                __DIR__.'/../Fixtures/testWrite3.csv',
                array(
                    array('nom', 'prénom', 'age'),
                    array('Martin', 'Durand', '28,5'),
                    array('Alain', 'Richard', '36'),
                ),
                'nom,prénom,age'."\n".
                'Martin,Durand,"28,5"'."\n".
                'Alain,Richard,36'."\n",
            ),
            // Data set #1
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => true,
                    'eol' => "\n", 
                    'escape' => "\\", 
                ),
                __DIR__.'/../Fixtures/testWrite4.csv',
                array(
                    array('nom', 'prénom', 'age','desc'),
                    array('Martin', 'Durand', '28', '"5\'10""'),
                    array('Alain', 'Richard', '36,5', '"5\'30""'),
                ),
                'nom,prénom,age,desc'."\n".
                'Martin,Durand,28,"""5\'10"""""'."\n".
                'Alain,Richard,"36,5","""5\'30"""""'."\n",
            ),
            // Data set #2
            array(
                array(
                    'delimiter' => ',', 
                    'enclosure' => '"', 
                    'encoding' => 'UTF-8', 
                    'eol' => "\n", 
                    'escape' => "\\", 
                    'enclosing_mode' => Dialect::ENCLOSING_MINIMAL,
                    'escape_double' => false,
                ),
                __DIR__.'/../Fixtures/testWrite4.csv',
                array(
                    array('nom', 'prénom', 'age','desc'),
                    array('Martin', 'Durand', '28,5', '"5\'10""
 tall'),
                    array('Alain', 'Richard', '36', '"5\'30""'),
                ),
                'nom,prénom,age,desc'."\n".
                'Martin,Durand,"28,5","\"5\'10\"\"'."\n".
                ' tall"'."\n".
                'Alain,Richard,36,"\"5\'30\"\""'."\n",
            ),
        );
    }

    public function testGetHttpHeaders()
    {
        $this->assertEquals(array(
            'Content-Type' => 'application/csv',
            'Content-Disposition' => 'attachment;filename="test.csv"',
                ), $this->writer->getHttpHeaders('test.csv'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWritingLineNoFilename()
    {
        $this->writer->writeRow(array('nom', 'prénom', 'age'));
    }

    /**
     * @dataProvider providerWritingBom
     */
    public function testWritingBom($options, $filename, $expectedCsv)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        $realOptions = array(
            'delimiter' => ',', 
            'enclosure' => '"', 
            'encoding' => $options[0], 
            'eol' => "\n", 
            'escape' => "\\", 
            'bom' => $options[1],
        );
        
        $this->writer = new CsvWriter($realOptions);

        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->open($filename));
        $this->assertEquals($expectedCsv, file_get_contents($filename));
        $this->assertInstanceOf('Spyrit\LightCsv\CsvWriter', $this->writer->close());

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function providerWritingBom()
    {
        return array(
            array(
                array('UTF-8', true),
                __DIR__.'/../Fixtures/testWriteBom1.csv',
                "\xEF\xBB\xBF",
            ),
            array(
                array('UTF-8', false),
                __DIR__.'/../Fixtures/testWriteBom2.csv',
                '',
            ),
            array(
                array('CP1252', true),
                __DIR__.'/../Fixtures/testWriteBom3.csv',
                '',
            ),
        );
    }
}
