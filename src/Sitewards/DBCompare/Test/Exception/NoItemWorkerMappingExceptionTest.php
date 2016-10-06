<?php

namespace Sitewards\DBCompare\Test\Exception;

use Sitewards\DBCompare\Exception\NoItemWorkerMappingException;

class NoItemWorkerMappingExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionMessage()
    {
        $sExpectedResult = 'No Item Worker found for type: test';
        $oException = new NoItemWorkerMappingException('test');
        $this->assertEquals($sExpectedResult, $oException->getMessage());
    }
}