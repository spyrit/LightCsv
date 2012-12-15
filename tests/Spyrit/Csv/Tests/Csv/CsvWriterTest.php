<?php

namespace Spyrit\Csv\Tests\Csv;

use Spyrit\Csv\Tests\Test\AbstractCsvTestCase;
use Spyrit\Csv\CsvWriter;

/**
 * CsvWriterTest
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class CsvWriterTest extends AbstractCsvTestCase
{
    /**
     *
     * @var Spyrit\Csv\CsvWriter
     */
    protected $writer;

    protected function setUp()
    {
        $this->writer = new CsvWriter();
    }

    public function testConstruct()
    {
        $this->assertEquals('wb', $this->getFileHandlerModeValue($this->writer));
        $this->assertEquals(';', $this->writer->getDelimiter());
        $this->assertEquals('"', $this->writer->getEnclosure());
        $this->assertEquals('CP1252', $this->writer->getEncoding());
        $this->assertEquals("\r\n", $this->writer->getLineEndings());
        $this->assertEquals("\\", $this->writer->getEscape());
        $this->assertEquals(false, $this->writer->getUseBom());
    }

    /**
     * @dataProvider providerWritingLine
     */
    public function testWritingLine($options, $filename, $line, $expectedCsv)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        $this->writer = new CsvWriter($options[0], $options[1], $options[2], $options[3]);

        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->open($filename));
        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->addLine($line));
        $this->assertEquals($expectedCsv, file_get_contents($filename));
        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->close());

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function providerWritingLine()
    {
        return array(
            array(
                array(',','"', 'UTF-8', "\n"),
                __DIR__.'/../Fixtures/testWrite.csv',
                array('Martin','Durand','28'),
                '"Martin","Durand","28"'."\n",
            ),
        );
    }

    /**
     * @dataProvider providerWritingLines
     */
    public function testWritingLines($options, $filename, $lines, $expectedCsv)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        $this->writer = new CsvWriter($options[0], $options[1], $options[2], $options[3]);

        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->setFilename($filename));
        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->addLines($lines));
        $this->assertEquals($expectedCsv, file_get_contents($filename));
        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->close());

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function providerWritingLines()
    {
        return array(
            array(
                array(',','"', 'UTF-8', "\n"),
                __DIR__.'/../Fixtures/testWrite.csv',
                array(
                    array('nom','prenom','age'),
                    array('Martin','Durand','28'),
                    array('Alain','Richard','36'),
                ),
                '"nom","prenom","age"'."\n".'"Martin","Durand","28"'."\n".'"Alain","Richard","36"'."\n",
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
        $this->writer->addLine(array('nom','prenom','age'));
    }

    /**
     * @dataProvider providerWritingBom
     */
    public function testWritingBom($options, $filename, $expectedCsv)
    {
        if (file_exists($filename)) {
            unlink($filename);
        }

        $this->writer = new CsvWriter(',','"', $options[0], "\n", "\\",$options[1]);

        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->open($filename));
        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->writeBom());
        $this->assertEquals($expectedCsv, file_get_contents($filename));
        $this->assertInstanceOf('Spyrit\Csv\CsvWriter',$this->writer->close());

        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    public function providerWritingBom()
    {
        return array(
            array(
                array('UTF-8', true),
                __DIR__.'/../Fixtures/testWrite.csv',
                "\xEF\xBB\xBF",
            ),
            array(
                array('UTF-8', false),
                __DIR__.'/../Fixtures/testWrite.csv',
                '',
            ),
            array(
                array('CP1252', true),
                __DIR__.'/../Fixtures/testWrite.csv',
                '',
            ),
        );
    }

    /**
     * @dataProvider providerGetSetUseBom
     */
    public function testGetSetUseBom($input,$expected)
    {
        $this->assertInstanceOf('Spyrit\Csv\AbstractCsv',$this->writer->setUseBom($input));
        $this->assertEquals($expected,$this->writer->getUseBom());
    }

    public function providerGetSetUseBom()
    {
        return array(
            array(null,false),
            array(0,false),
            array('',false),
            array(false,false),
            array(1,true),
            array(true,true),
        );
    }
}
