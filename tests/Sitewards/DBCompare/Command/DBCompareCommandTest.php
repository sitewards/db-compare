<?php

namespace Sitewards\DBCompare\Command;

class DBCompareCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the configure function sets name, description and help
     */
    public function testConfigure()
    {
        $oDBCompareCommand = new DBCompareCommand();
        $this->assertEquals('db:compare', $oDBCompareCommand->getName());
        $this->assertEquals('Compare two database and get a diff file', $oDBCompareCommand->getDescription());
        $this->assertEquals(
            'This command compares two database files and produces a difference file that can be use to migrate changes',
            $oDBCompareCommand->getHelp()
        );
    }
}