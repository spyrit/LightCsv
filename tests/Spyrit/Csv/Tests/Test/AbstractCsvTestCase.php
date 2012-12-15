<?php

namespace Spyrit\Csv\Tests\Test;

use Spyrit\Csv\AbstractCsv;

/**
 * AbstractCsvTestCase
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
abstract class AbstractCsvTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * get fileHandler (non public access) value for unit tests
     *
     * @param Spyrit\Csv\AbstractCsv
     *
     * @return mixed
     */
    protected function getFileHandlerValue($structure)
    {
        $reflection = new \ReflectionClass($structure);
        $prop = $reflection->getProperty('fileHandler');
        $prop->setAccessible(true);

        return $prop->getValue($structure);
    }

    /**
     * get fileHandler mode (non public access) value for unit tests
     *
     * @param Spyrit\Csv\AbstractCsv
     *
     * @return mixed
     */
    protected function getFileHandlerModeValue($structure)
    {
        $reflection = new \ReflectionClass($structure);
        $prop = $reflection->getProperty('fileHandlerMode');
        $prop->setAccessible(true);

        return $prop->getValue($structure);
    }

}
