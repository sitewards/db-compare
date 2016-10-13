<?php

namespace Sitewards\DBCompare\Question;

use Sitewards\DBCompare\Validator\FilePath;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

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

        $oValidator      = $this->createMock(FilePath::class);

        $oWorkerQuestion = new FileQuestion(
            $oQuestionHelper,
            $oValidator
        );
        $sWorkerName = $oWorkerQuestion->getFilePath(
            $oInput,
            $oOutput,
            'Test Question'
        );
        $this->assertEquals('sample.sql', $sWorkerName);
    }
}