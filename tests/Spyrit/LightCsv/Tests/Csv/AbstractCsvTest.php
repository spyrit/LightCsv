<?php

namespace Spyrit\LightCsv\Tests\Csv;

use Spyrit\LightCsv\AbstractCsv;
use Spyrit\LightCsv\Tests\Test\AbstractCsvTestCase;

/**
 * AbstractCsvTest
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class AbstractCsvTest extends AbstractCsvTestCase
{
    /**
     *
     * @var Spyrit\LightCsv\AbstractCsv
     */
    protected $structure;

    protected function setUp()
    {
        $this->structure = $this->getMockForAbstractClass('Spyrit\LightCsv\AbstractCsv');
    }

    /**
     * @dataProvider providerGetSetLineEndings
     */
    public function testGetSetLineEndings($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setLineEndings($input));
        $this->assertEquals($expected, $this->structure->getLineEndings());
    }

    public function providerGetSetLineEndings()
    {
        return array(
            array(null, "\r\n"),
            array('', "\r\n"),
            array("\r\n", "\r\n"),
            array('windows', "\r\n"),
            array('win', "\r\n"),
            array('linux', "\n"),
            array('unix', "\n"),
            array("\n", "\n"),
            array('mac', "\r"),
            array('macos', "\r"),
            array("\r", "\r"),
        );
    }

    /**
     * @dataProvider providerGetSetDelimiter
     */
    public function testGetSetDelimiter($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setDelimiter($input));
        $this->assertEquals($expected, $this->structure->getDelimiter());
    }

    public function providerGetSetDelimiter()
    {
        return array(
            array(null, ';'),
            array('', ';'),
            array(',', ","),
        );
    }

    /**
     * @dataProvider providerGetSetEnclosure
     */
    public function testGetSetEnclosure($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setEnclosure($input));
        $this->assertEquals($expected, $this->structure->getEnclosure());
    }

    public function providerGetSetEnclosure()
    {
        return array(
            array(null, '"'),
            array('', '"'),
            array('\'', '\''),
        );
    }

    /**
     * @dataProvider providerGetSetEscape
     */
    public function testGetSetEscape($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setEscape($input));
        $this->assertEquals($expected, $this->structure->getEscape());
    }

    public function providerGetSetEscape()
    {
        return array(
            array(null, "\\"),
            array('', "\\"),
            array('"', '"'),
        );
    }

    /**
     * @dataProvider providerGetSetEncoding
     */
    public function testGetSetEncoding($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setEncoding($input));
        $this->assertEquals($expected, $this->structure->getEncoding());
    }

    public function providerGetSetEncoding()
    {
        return array(
            array(null, 'CP1252'),
            array('', 'CP1252'),
            array('UTF-8', 'UTF-8'),
        );
    }

    /**
     * @dataProvider providerGetSetUseBom
     */
    public function testGetSetUseBom($input, $expected)
    {
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->setUseBom($input));
        $this->assertEquals($expected, $this->structure->getUseBom());
    }

    public function providerGetSetUseBom()
    {
        return array(
            array(null, false),
            array(0, false),
            array('', false),
            array(false, false),
            array(1, true),
            array(true, true),
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
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $this->structure->open(__DIR__.'/../Fixtures/test1.csv'));
        $this->assertInternalType('resource', $this->getFileHandlerValue($this->structure));

        return $this->structure;
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
        $this->assertInstanceOf('Spyrit\LightCsv\AbstractCsv', $structure->close());
        $this->assertNull($this->getFileHandlerValue($structure));
    }
}
