<?php

namespace Sitewards\DBCompare\Command;

use Sitewards\DBCompare\Question\FileQuestion;
use Sitewards\DBCompare\Question\WorkerQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

class DBCompareCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the configure function sets name, description and help
     */
    public function testConfigure()
    {
        $oQuestionHelper = $this->createMock(QuestionHelper::class);

        $oFileQuestion = $this->getMockBuilder(FileQuestion::class)
            ->setConstructorArgs([$oQuestionHelper])
            ->getMock();

        $oWorkerQuestion = $this->getMockBuilder(WorkerQuestion::class)
            ->setConstructorArgs([$oQuestionHelper])
            ->getMock();

        $oDBCompareCommand = new DBCompareCommand(
            $oFileQuestion,
            $oWorkerQuestion
        );

        $this->assertEquals('db:compare', $oDBCompareCommand->getName());
        $this->assertEquals('Compare two database and get a diff file', $oDBCompareCommand->getDescription());
        $this->assertEquals(
            'This command compares two database files and produces a difference file that can be use to migrate changes',
            $oDBCompareCommand->getHelp()
        );
    }
}