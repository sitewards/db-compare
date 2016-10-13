<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class WorkerQuestion
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
     * @return string
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function getMergeWorker(
        InputInterface $oInput,
        OutputInterface $oOutput
    )
    {
        $oItemQuestion = new ChoiceQuestion(
            'Please select the item you wish to merge',
            ['system config', 'cms page', 'cms block'],
            '0'
        );

        return $this->oQuestionHelper->ask($oInput, $oOutput, $oItemQuestion);
    }
}