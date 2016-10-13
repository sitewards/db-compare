<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

class WorkerQuestionTest extends \PHPUnit_Framework_TestCase
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
            ->willReturn('system config');

        $oInput = $this->createMock(Input::class);
        $oOutput = $this->createMock(Output::class);

        $oWorkerQuestion = new WorkerQuestion($oQuestionHelper);
        $sWorkerName = $oWorkerQuestion->getMergeWorker(
            $oInput,
            $oOutput
        );
        $this->assertEquals('system config', $sWorkerName);
    }
}