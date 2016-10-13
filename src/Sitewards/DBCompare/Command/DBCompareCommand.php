<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Command;

use Sitewards\DBCompare\Worker\DBWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;
use Doctrine\DBAL\Exception\ConnectionException;

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
     * @return null
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        try {
            $oOutput->writeln('Staring the db:compare');

            $sMainDBPath = $this->getFilePath($oInput, $oOutput, 'Please enter the main database file path:');
            $sMergingDB = $this->getFilePath($oInput, $oOutput, 'Please enter the merging database file path:');
            $iItemToMerge = $this->getItemToMerge($oInput, $oOutput);

            $sDBUser = $this->getDBInformation($oInput, $oOutput, 'Please enter a valid local database user:');
            $sDBPassword = $this->getSensitiveDBInformation(
                $oInput,
                $oOutput,
                'Please enter a valid local database password:'
            );

            $oDBWorker = new DBWorker($sDBUser, $sDBPassword, $iItemToMerge);
            $oDBWorker->buildTempDatabases();
            $oDBWorker->insertFromFile(DBWorker::S_MAIN_DB_NAME, $sMainDBPath);
            $oDBWorker->insertFromFile(DBWorker::S_MERGE_DB_NAME, $sMergingDB);
            $oDBWorker->getDifferencesInDatabase();
            $oDBWorker->cleanTempDatabases();

            $oOutput->writeln('Ending the db:compare');
        } catch (ConnectionException $oException) {
            $oFormatter = $this->getHelper('formatter');
            $oOutput->writeln(
                $oFormatter->formatBlock(
                    'The database information provided is not valid',
                    'error'
                )
            );
        } catch (\Exception $oException) {
            $oFormatter = $this->getHelper('formatter');
            $oOutput->writeln(
                $oFormatter->formatBlock(
                    $oException->getMessage(),
                    'error'
                )
            );
        }
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    private function getFilePath(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper = $this->getHelper('question');
        $oFilePathQuestion = new Question($sQuestion);
        $oFilePathQuestion->setValidator(
            function ($sAnswer) {
                if (!file_exists($sAnswer)) {
                    throw new FileNotFoundException(
                        sprintf(
                            'The file %s cannot be found',
                            $sAnswer
                        )
                    );
                }
                if (!is_readable($sAnswer)) {
                    throw new FileNotReadableException(
                        sprintf(
                            'The file %s cannot be read',
                            $sAnswer
                        )
                    );
                }
                return $sAnswer;
            }
        );
        return $oQuestionHelper->ask($oInput, $oOutput, $oFilePathQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    private function getDBInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper = $this->getHelper('question');
        $oDBInfoQuestion = new Question($sQuestion);
        return $oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @param string $sQuestion
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    private function getSensitiveDBInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    ) {
        $oQuestionHelper = $this->getHelper('question');
        $oDBInfoQuestion = new Question($sQuestion);
        $oDBInfoQuestion->setHidden(true);
        return $oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
    }

    /**
     * @param InputInterface $oInput
     * @param OutputInterface $oOutput
     * @return string
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    private function getItemToMerge(InputInterface $oInput, OutputInterface $oOutput)
    {
        $oQuestionHelper = $this->getHelper('question');
        $oItemQuestion = new ChoiceQuestion(
            'Please select the item you wish to merge',
            ['system config', 'cms page', 'cms block'],
            '0'
        );

        return $oQuestionHelper->ask($oInput, $oOutput, $oItemQuestion);
    }
}