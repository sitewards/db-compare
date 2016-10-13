<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;

class FileQuestionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function testGetMergeWorker()
    {
        $oQuestionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $oQuestionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('sample.sql');

        $oInput = $this->createMock(Input::class);
        $oOutput = $this->createMock(Output::class);

        $oWorkerQuestion = new FileQuestion($oQuestionHelper);
        $sWorkerName = $oWorkerQuestion->getFilePath(
            $oInput,
            $oOutput,
            'Test Question'
        );
        $this->assertEquals('sample.sql', $sWorkerName);
    }

    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function testGetMergeWorkerFileNotFoundException()
    {
        $oQuestionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $oQuestionHelper->expects($this->once())
            ->method('ask')
            ->willThrowException(new FileNotFoundException());

        $this->expectException(FileNotFoundException::class);
        $oInput = $this->createMock(Input::class);
        $oOutput = $this->createMock(Output::class);

        $oWorkerQuestion = new FileQuestion($oQuestionHelper);
        $oWorkerQuestion->getFilePath(
            $oInput,
            $oOutput,
            'Test Question'
        );
    }
}