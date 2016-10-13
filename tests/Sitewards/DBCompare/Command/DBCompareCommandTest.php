<?php

namespace Sitewards\DBCompare\Command;

use Sitewards\DBCompare\Question\FileQuestion;
use Sitewards\DBCompare\Question\WorkerQuestion;

class DBCompareCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the configure function sets name, description and help
     */
    public function testConfigure()
    {
        $oDBCompareCommand = new DBCompareCommand(
            new FileQuestion(),
            new WorkerQuestion()
        );
        $this->assertEquals('db:compare', $oDBCompareCommand->getName());
        $this->assertEquals('Compare two database and get a diff file', $oDBCompareCommand->getDescription());
        $this->assertEquals(
            'This command compares two database files and produces a difference file that can be use to migrate changes',
            $oDBCompareCommand->getHelp()
        );
    }
}