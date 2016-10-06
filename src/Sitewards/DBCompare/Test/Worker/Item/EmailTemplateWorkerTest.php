<?php

namespace Sitewards\DBCompare\Test\Worker\Item;

use Sitewards\DBCompare\Worker\Item\EmailTemplateWorker;

class EmailTemplateWorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the connection is the correct instance of
     */
    public function testGetConnection()
    {
        $oEmailItemWorker = new EmailTemplateWorker(
            $this->createMock('\Doctrine\DBAL\Connection'),
            'sample-diff.sql'
        );
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $oEmailItemWorker->getConnection());
    }

    /**
     * Test that the file name is as expected
     */
    public function testGetDiffFileName()
    {
        $sExpectedResult = 'sample-diff.sql';
        $oEmailItemWorker = new EmailTemplateWorker(
            $this->createMock('\Doctrine\DBAL\Connection'),
            $sExpectedResult
        );
        $this->assertEquals($sExpectedResult, $oEmailItemWorker->getDiffFileName());
    }

    /**
     * Test that when given a null the construct throws an exception
     */
    public function testExceptionOnConstruct()
    {
        $this->expectException('Sitewards\DBCompare\Exception\NoDiffFileNameGivenException');
        $oEmailItemWorker = new EmailTemplateWorker(
            $this->createMock('\Doctrine\DBAL\Connection'),
            null
        );
    }
}