<?php

namespace Sitewards\DBCompare\Exception;

class NoItemWorkerMappingExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $oNoItemWorkerMappingException = new NoItemWorkerMappingException('test');
        $this->assertEquals(
            'No Item Worker found for type: test',
            $oNoItemWorkerMappingException->getMessage()
        );
    }
}