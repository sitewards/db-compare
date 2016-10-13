<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

class DBQuestionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function testGetBasicInformation()
    {
        $oQuestionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $oQuestionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('db-user');

        $oInput = $this->createMock(Input::class);
        $oOutput = $this->createMock(Output::class);

        $oWorkerQuestion = new DBQuestion($oQuestionHelper);
        $sWorkerName = $oWorkerQuestion->getBasicInformation(
            $oInput,
            $oOutput
        );
        $this->assertEquals('db-user', $sWorkerName);
    }

    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function testGetSensitiveDBInformation()
    {
        $oQuestionHelper = $this->getMockBuilder(QuestionHelper::class)->getMock();
        $oQuestionHelper->expects($this->once())
            ->method('ask')
            ->willReturn('db-password');

        $oInput = $this->createMock(Input::class);
        $oOutput = $this->createMock(Output::class);

        $oWorkerQuestion = new DBQuestion($oQuestionHelper);
        $sWorkerName = $oWorkerQuestion->getSensitiveDBInformation(
            $oInput,
            $oOutput
        );
        $this->assertEquals('db-password', $sWorkerName);
    }
}