<?php

namespace Sitewards\DBCompare\Command;

use Sitewards\DBCompare\Factory\ItemWorkerFactory;
use Sitewards\DBCompare\Question\DBQuestion;
use Sitewards\DBCompare\Question\FileQuestion;
use Sitewards\DBCompare\Question\WorkerQuestion;
use Sitewards\DBCompare\Validator\FilePath;
use Symfony\Component\Console\Helper\QuestionHelper;

class DBCompareCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the configure function sets name, description and help
     */
    public function testConfigure()
    {
        $oQuestionHelper = $this->createMock(QuestionHelper::class);
        $oValidator      = $this->createMock(FilePath::class);

        $oFileQuestion = $this->getMockBuilder(FileQuestion::class)
            ->setConstructorArgs([$oQuestionHelper, $oValidator])
            ->getMock();

        $oWorkerQuestion = $this->getMockBuilder(WorkerQuestion::class)
            ->setConstructorArgs([$oQuestionHelper])
            ->getMock();

        $oDBQuestion = $this->getMockBuilder(DBQuestion::class)
            ->setConstructorArgs([$oQuestionHelper])
            ->getMock();

        $oItemFactory = $this->getMockBuilder(ItemWorkerFactory::class)
            ->getMock();

        $oDBCompareCommand = new DBCompareCommand(
            $oFileQuestion,
            $oWorkerQuestion,
            $oDBQuestion,
            $oItemFactory
        );

        $this->assertEquals('db:compare', $oDBCompareCommand->getName());
        $this->assertEquals('Compare two database and get a diff file', $oDBCompareCommand->getDescription());
        $this->assertEquals(
            'This command compares two database files and produces a difference file that can be use to migrate changes',
            $oDBCompareCommand->getHelp()
        );
    }
}