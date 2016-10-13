<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DBQuestion
{
    /** @var QuestionHelper */
    private $oQuestionHelper;

    /**
     * @param QuestionHelper $oQuestionHelper
     */
    public function __construct(
        QuestionHelper $oQuestionHelper
    ) {
        $this->oQuestionHelper = $oQuestionHelper;
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function getBasicInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oDBInfoQuestion = new Question($sQuestion);
        return $this->oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function getSensitiveDBInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oDBInfoQuestion = new Question($sQuestion);
        $oDBInfoQuestion->setHidden(true);
        return $this->oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
    }
}