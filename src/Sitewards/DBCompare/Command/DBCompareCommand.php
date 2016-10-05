<?php

/**
 * @category    Sitewards
 * @package     Sitewards_DBCompare
 * @copyright   Copyright (c) Sitewards GmbH (http://www.sitewards.com/)
 */

namespace Sitewards\DBCompare\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Sitewards\DBCompare\Exception\FileNotFoundException;
use Sitewards\DBCompare\Exception\FileNotReadableException;

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
        $oOutput->writeln('Merging item: ' . $sItemToMerge);

        $sDBUser     = $this->getDBInformation($oInput, $oOutput, 'Please enter a valid local database user:');
        $sDBPassword = $this->getSensitiveDBInformation($oInput, $oOutput, 'Please enter a valid local database password:');

        $oOutput->writeln('DB User: ' . $sDBUser);
        $oOutput->writeln('DB Password: ' . $sDBPassword);

        $oDBConnection = $this->getDatabaseConnection($sDBUser, $sDBPassword);
        $this->buildTempDatabases($oDBConnection);
        $this->cleanTempDatabases($oDBConnection);

        $oOutput->writeln('Ending the db:compare');
    }

    /**
     * @param Connection $oDBConnection
     */
    private function buildTempDatabases(Connection $oDBConnection)
    {
        $oSchema = $oDBConnection->getSchemaManager();
        $oSchema->dropAndCreateDatabase('db_comp_main_db');
        $oSchema->dropAndCreateDatabase('db_comp_merge_db');
    }

    /**
     * @param Connection $oDBConnection
     */
    private function cleanTempDatabases(Connection $oDBConnection)
    {
        $oSchema = $oDBConnection->getSchemaManager();
        $oSchema->dropDatabase('db_comp_main_db');
        $oSchema->dropDatabase('db_comp_merge_db');
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
                if (!file_exists($sAnswer)) {
                    throw new FileNotFoundException('The file given cannot be found');
                }
                if (!is_readable($sAnswer)) {
                    throw new FileNotReadableException('The file given cannot be read');
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
     */
    private function getSensitiveDBInformation(
        InputInterface $oInput,
        OutputInterface $oOutput,
        $sQuestion = ''
    )
    {
        $oQuestionHelper = $this->getHelper('question');
        $oDBInfoQuestion = new Question($sQuestion);
        $oDBInfoQuestion->setHidden(true);
        return $oQuestionHelper->ask($oInput, $oOutput, $oDBInfoQuestion);
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

    /**
     * @param $sDBUser
     * @param $sDBPassword
     * @return Connection
     */
    private function getDatabaseConnection($sDBUser, $sDBPassword)
    {
        $oDBConfig = new Configuration();
        $aConnectionParams = [
            'user' => $sDBUser,
            'password' => $sDBPassword,
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];
        return DriverManager::getConnection($aConnectionParams, $oDBConfig);
    }
}