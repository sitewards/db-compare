<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Sitewards\DBCompare\Exception\FileNotFoundException;

class DBCompareCommand extends Command
{
    /**
     * Set-up the db:compare command
     */
    protected function configure()
    {
        $this->setName('db:compare');
        $this->setDescription('Compare two database and get a diff file');
        $this->setHelp(
            'This command compares two database files and produces a difference file that can be use to migrate changes'
        );
    }

    /**
     * Execution of the db:compare command
     *
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        $oOutput->writeln('Staring the db:compare');

        $sMainDBPath  = $this->getFilePath($oInput, $oOutput, 'Please enter the main database file path:');
        $sMergingDB   = $this->getFilePath($oInput, $oOutput, 'Please enter the merging database file path:');
        $sItemToMerge = $this->getItemToMerge($oInput, $oOutput);

        $oOutput->writeln('Main DB file: ' . $sMainDBPath);
        $oOutput->writeln('Merging DB file: ' . $sMergingDB);
        $oOutput->writeln('Merging item : ' . $sItemToMerge);
        $oOutput->writeln('Ending the db:compare');
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     */
    private function getFilePath(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper   = $this->getHelper('question');
        $oFilePathQuestion = new Question($sQuestion);
        $oFilePathQuestion->setValidator(
            function ($sAnswer) {
                if (!is_file($sAnswer)) {
                    throw new FileNotFoundException('The file given cannot be found');
                }
                return $sAnswer;
            }
        );
        return $oQuestionHelper->ask($oInput, $oOutput, $oFilePathQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @return string
     */
    private function getItemToMerge(InputInterface $oInput, OutputInterface $oOutput)
    {
        $oQuestionHelper = $this->getHelper('question');
        $oItemQuestion   = new ChoiceQuestion(
            'Please select the item you wish to merge',
            ['cms pages', 'cms blocks', 'system config'],
            '0'
        );

        return $oQuestionHelper->ask($oInput, $oOutput, $oItemQuestion);
    }
}