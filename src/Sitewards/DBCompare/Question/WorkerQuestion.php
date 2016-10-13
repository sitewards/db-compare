<?php

namespace Sitewards\DBCompare\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class WorkerQuestion
{

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function getMergeWorker(
        QuestionHelper $oQuestionHelper,
        InputInterface $oInput,
        OutputInterface $oOutput
    )
    {
        $oItemQuestion = new ChoiceQuestion(
            'Please select the item you wish to merge',
            ['system config', 'cms page', 'cms block'],
            '0'
        );

        return $oQuestionHelper->ask($oInput, $oOutput, $oItemQuestion);
    }
}