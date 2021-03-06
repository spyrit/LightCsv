<?php

namespace Spyrit\LightCsv\Tests\Csv;

use Spyrit\LightCsv\AbstractCsv;
use Spyrit\LightCsv\Dialect;
use Spyrit\LightCsv\Tests\AbstractCsvTestCase;

/**
 * AbstractCsvTest
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class AbstractCsvTest extends AbstractCsvTestCase
{
    /**
     *
     * @var AbstractCsv
     */
    protected $structure;

    protected function setUp()
    {
        $this->structure = $this->getMockForAbstractClass('Spyrit\LightCsv\AbstractCsv');
    }

    /**
     * @dataProvider providerGetSetDialect
     */
    public function testGetSetDialect($input)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setDialect($input));
        $this->assertInstanceOf('\\Spyrit\\LightCsv\\Dialect', $this->structure->getDialect());
    }

    public function providerGetSetDialect()
    {
        return array(
            array(new Dialect()),
        );
    }
    
    /**
     * @dataProvider providerGetSetFilename
     */
    public function testGetSetFilename($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setFilename($input));
        $this->assertEquals($expected, $this->structure->getFilename());
    }

    public function providerGetSetFilename()
    {
        return array(
            array(null, null),
            array('', ''),
            array(__DIR__.'/../Fixtures/test1.csv', __DIR__.'/../Fixtures/test1.csv'),
        );
    }

    public function testOpen()
    {
        $this->assertFalse($this->structure->isFileOpened());
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->open(__DIR__.'/../Fixtures/test1.csv'));
        $this->assertTrue($this->structure->isFileOpened());
        $this->assertInternalType('resource', $this->getFileHandlerValue($this->structure));

        return $this->structure;
    }

    public function testOpenNewFile()
    {
        $file1 = __DIR__.'/../Fixtures/test1.csv';
        $file2 = __DIR__.'/../Fixtures/test2.csv';

        $this->assertFalse($this->structure->isFileOpened());
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->open($file1));
        $this->assertEquals($file1, $this->structure->getFilename());
        $this->assertTrue($this->structure->isFileOpened());
        $fileHandler1 = $this->getFileHandlerValue($this->structure);
        $this->assertInternalType('resource', $fileHandler1);

        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->open($file2));
        $this->assertEquals($file2, $this->structure->getFilename());
        $this->assertTrue($this->structure->isFileOpened());
        $this->assertNotInternalType('resource', $fileHandler1);
        $fileHandler2 = $this->getFileHandlerValue($this->structure);
        $this->assertInternalType('resource', $fileHandler2);

        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->close());
        $this->assertFalse($this->structure->isFileOpened());
        $this->assertNotInternalType('resource', $fileHandler2);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOpenNoFilename()
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->open());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOpenNoExistingFile()
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->open(__DIR__.'/../Fixtures/abc.csv'));
    }

    /**
     * @depends testOpen
     */
    public function testClose($structure)
    {
        $this->assertTrue($structure->isFileOpened());
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $structure->close());
        $this->assertFalse($structure->isFileOpened());
        $this->assertNotInternalType('resource', $this->getFileHandlerValue($structure));
    }
}
